<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Crm\Service\Container,
    Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Entity\UrlManager;



class WorkprojectsList extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params){
        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:project.list",
            "",
            $params
        );

        return ob_get_clean();
    }

    function getMyAlowedProjectIds(){

        return array_column(Orm\ProjectUserPermissionTable::getList([
            'filter' => [ 'ENTITY' => Entity\EditorManager::getCurrentUserPermissionEntities() ],
            'select' => ['PROJECT_ID'],
            'group' => ['PROJECT_ID'],
        ])->fetchAll(), 'PROJECT_ID');
    }

    function defineProjects(){
        if(!Loader::includeModule("gpi.workproject"))
            return;

        $entity = new Orm\ProjectTable();
        $viewPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['project.list.item'];
        $editPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['project.list.item.edit'];

        $this->arResult['PROJECTS'] = $entity::getList([
            'select' => [
                '*', 'LAST_NAME' => 'USER.LAST_NAME', 'NAME' => 'USER.NAME', 'SECOND_NAME' => 'USER.SECOND_NAME', 'LINK', 'EDIT_LINK'
            ],
            'filter' => ['ID' => $this->getMyAlowedProjectIds()],
            'runtime' => [
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
                    ]
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['USER.LAST_NAME','USER.NAME', 'USER.SECOND_NAME']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK',
                    'REPLACE("'.$viewPath.'", "#project_id#", %s)',
                    ['ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE("'.$editPath.'", "#project_id#", %s)',
                    ['ID']
                ),
            ]
        ])->fetchAll();
    }

    function checkUpdates(){
        $groupUnionIds = array_column($this->arResult['PROJECTS'], 'GROUP_UNION_ID');
        $projectIds = array_column($this->arResult['PROJECTS'], 'ID');

        $projectGroups = Orm\GroupItemTable::getList([
            'filter' => [
                'UNION_ID' => $groupUnionIds
            ],
            'select' => [
                'ID', 'UNION_ID', 'AD_UNION_ID', 'FORUM_ID', 'CALENDAR_ID', 'STORAGE_ID'
            ]
        ])->fetchAll();


        $adUnionIds = array_merge(
            array_column($this->arResult['PROJECTS'], 'AD_UNION_ID'),
            array_column($projectGroups, 'AD_UNION_ID')
        );
        $forumIds = array_merge(
            array_column($this->arResult['PROJECTS'], 'FORUM_ID'),
            array_column($projectGroups, 'FORUM_ID')
        );

        $calendarIds =  array_merge(
            array_column($this->arResult['PROJECTS'], 'CALENDAR_ID'),
            array_column($projectGroups, 'CALENDAR_ID')
        );

        $storageIds = array_merge(
            array_column($this->arResult['PROJECTS'], 'STORAGE_ID'),
            array_column($projectGroups, 'STORAGE_ID')
        );

        $updates = Orm\ProjectDirectionTable::getList([
            'select' => [
                'UP_EN_ID' => 'UPDATE.ENTITY_ID',
                'UP_EN_TYPE' => 'UPDATE.ENTITY_TYPE',
                'UP_ID' => 'UPDATE.ID',
                'PROJECT_ID'
            ],
            'filter' => [
                'PROJECT_ID' => $projectIds,
                'UPDATE.ENTITY_TYPE' => 'ProjectDirection',
            ],
            'runtime' => [
                'UPDATE' => [
                    'data_type' => 'Gpi\Workproject\Orm\EntityUpdateTable',
                    'reference' => [
                        'this.ID' => 'ref.ENTITY_ID',
                    ]
                ]
            ]
        ])->fetchAll();

        if($adUnionIds)
            $updates = array_merge(Orm\AdItemTable::getList([
                'select' => [
                    'UP_EN_ID' => 'UPDATE.ENTITY_ID',
                    'UP_EN_TYPE' => 'UPDATE.ENTITY_TYPE',
                    'UP_ID' => 'UPDATE.ID',
                    'UNION_ID'
                ],
                'filter' => [
                    'UNION_ID' => $adUnionIds,
                    'UPDATE.ENTITY_TYPE' => 'AdItem',
                ],
                'runtime' => [
                    'UPDATE' => [
                        'data_type' => 'Gpi\Workproject\Orm\EntityUpdateTable',
                        'reference' => [
                            'this.ID' => 'ref.ENTITY_ID',
                        ]
                    ]
                ]
            ])->fetchAll(), $updates);

        if($forumIds)
            $updates = array_merge( Orm\ForumDiscussionTable::getList([
                'select' => [
                    'UP_EN_ID' => 'UPDATE.ENTITY_ID',
                    'UP_EN_TYPE' => 'UPDATE.ENTITY_TYPE',
                    'UP_ID' => 'UPDATE.ID',
                    'FORUM_ID'
                ],
                'filter' => [
                    'FORUM_ID' => $forumIds,
                    'UPDATE.ENTITY_TYPE' => 'ForumDiscussion',
                ],
                'runtime' => [
                    'UPDATE' => [
                        'data_type' => 'Gpi\Workproject\Orm\EntityUpdateTable',
                        'reference' => [
                            'this.ID' => 'ref.ENTITY_ID',
                        ],
                    ]
                ]
            ])->fetchAll(), $updates);

        if($forumIds)
            $updates = array_merge(Orm\ForumDiscussionMessageTable::getList([
                'select' => [
                    'UP_EN_ID' => 'UPDATE.ENTITY_ID',
                    'UP_EN_TYPE' => 'UPDATE.ENTITY_TYPE',
                    'UP_ID' => 'UPDATE.ID',
                    'FORUM_ID' => 'DISCUSSION.FORUM_ID'
                ],
                'filter' => [
                    'DISCUSSION.FORUM_ID' => $forumIds,
                    'UPDATE.ENTITY_TYPE' => 'ForumDiscussionMessage',
                ],
                'runtime' => [
                    'DISCUSSION' => [
                        'data_type' => 'Gpi\Workproject\Orm\ForumDiscussionTable',
                        'reference' => [
                            'this.DISCUSSION_ID' => 'ref.ID',
                        ]
                    ],
                    'UPDATE' => [
                        'data_type' => 'Gpi\Workproject\Orm\EntityUpdateTable',
                        'reference' => [
                            'this.ID' => 'ref.ENTITY_ID',
                        ],
                    ]
                ]
            ])->fetchAll(), $updates);

        if($calendarIds)
            $updates = array_merge(Orm\CalendarEventTable::getList([
                'select' => [
                    'UP_EN_ID' => 'UPDATE.ENTITY_ID',
                    'UP_EN_TYPE' => 'UPDATE.ENTITY_TYPE',
                    'UP_ID' => 'UPDATE.ID',
                    'CALENDAR_ID'
                ],
                'filter' => [
                    'CALENDAR_ID' => $calendarIds,
                    'UPDATE.ENTITY_TYPE' => 'CalendarEvent',
                ],
                'runtime' => [
                    'UPDATE' => [
                        'data_type' => 'Gpi\Workproject\Orm\EntityUpdateTable',
                        'reference' => [
                            'this.ID' => 'ref.ENTITY_ID',
                        ],
                    ]
                ]
            ])->fetchAll(), $updates);

        if($storageIds)
            $updates = array_merge(Orm\StorageObjectTable::getList([
                'select' => [
                    'UP_EN_ID' => 'UPDATE.ENTITY_ID',
                    'UP_EN_TYPE' => 'UPDATE.ENTITY_TYPE',
                    'UP_ID' => 'UPDATE.ID',
                    'STORAGE_ID'
                ],
                'filter' => [
                    'STORAGE_ID' => $storageIds,
                    'UPDATE.ENTITY_TYPE' => 'StorageObject',
                ],
                'runtime' => [
                    'UPDATE' => [
                        'data_type' => 'Gpi\Workproject\Orm\EntityUpdateTable',
                        'reference' => [
                            'this.ID' => 'ref.ENTITY_ID',
                        ],
                    ]
                ]
            ])->fetchAll(), $updates);

        foreach ($updates as $update){
            switch ($update['UP_EN_TYPE']){
                case 'ProjectDirection':
                    $projectKey =  array_search($update['PROJECT_ID'], array_column($this->arResult['PROJECTS'], 'ID'));
                    break;

                case 'AdItem':
                    $key =  array_search($update['UNION_ID'], array_column($this->arResult['PROJECTS'], 'AD_UNION_ID'));
                    if(!$key){
                        $groupKey =  array_search($update['UNION_ID'], array_column($projectGroups, 'AD_UNION_ID'));
                        $key = array_search($projectGroups[$groupKey]['PROJECT_ID'], array_column($this->arResult['PROJECTS'], 'ID'));
                    }
                    break;

                case 'ForumDiscussionMessage':
                case 'ForumDiscussion':
                    $key =  array_search($update['FORUM_ID'], array_column($this->arResult['PROJECTS'], 'FORUM_ID'));
                    if(!$key){
                        $groupKey =  array_search($update['FORUM_ID'], array_column($projectGroups, 'FORUM_ID'));
                        $key = array_search($projectGroups[$groupKey]['PROJECT_ID'], array_column($this->arResult['PROJECTS'], 'ID'));
                    }
                    break;

                case 'CalendarEvent':
                    $key =  array_search($update['CALENDAR_ID'], array_column($this->arResult['PROJECTS'], 'CALENDAR_ID'));
                    if(!$key){
                        $groupKey =  array_search($update['CALENDAR_ID'], array_column($projectGroups, 'CALENDAR_ID'));
                        $key = array_search($projectGroups[$groupKey]['PROJECT_ID'], array_column($this->arResult['PROJECTS'], 'ID'));
                    }
                    break;

                case 'StorageObject':
                    $key =  array_search($update['STORAGE_ID'], array_column($this->arResult['PROJECTS'], 'STORAGE_ID'));
                    if(!$key){
                        $groupKey =  array_search($update['STORAGE_ID'], array_column($projectGroups, 'STORAGE_ID'));
                        $key = array_search($projectGroups[$groupKey]['PROJECT_ID'], array_column($this->arResult['PROJECTS'], 'ID'));
                    }
                    break;

            }

            $this->arResult['PROJECTS'][$projectKey]['IS_NEW'] = true;
            $this->arResult['PROJECTS'][$projectKey]['NEW_IDS'][] = $update['UP_ID'];
        }
    }

    function defineParams(){
        $this->arResult['PROJECT_CREATE_LINK'] = UrlManager::getProjectItemEditLink(0);
        $this->arResult['PERMISSIONS'] = Entity\EditorManager::defineProjectListPermission(array_column($this->arResult['PROJECTS'], 'ID'));
        $this->arResult['PROJECT_PATH'] = $this->arParams['SEF_FOLDER']. $this->arParams['URL_TEMPLATES']['project'];
        CJSCore::init(['ui.list', "sidepanel", 'ui.entity-selector']);
        $this->arResult['GRID_ID'] = 'workprojects_projects';
    }
    public function executeComponent() {

        if(!Loader::IncludeModule("iblock"))
            return;

        $this->defineProjects();
        $this->defineParams();
        $this->checkUpdates();

        $this->IncludeComponentTemplate();
    }

}
