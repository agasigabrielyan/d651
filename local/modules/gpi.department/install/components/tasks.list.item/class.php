<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Helper,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Entity\UrlManager,
    Bitrix\Main\Engine\Contract\Controllerable;


class RsTasksListItem extends \CBitrixComponent  implements Controllerable{

    public function configureActions(){}

    public static function loadEntityAction(){
        if (!Loader::includeModule("gpi.workproject"))
            return;

        return Helper\FormData::save(new Orm\TasksItemTable(), [], 1);
    }

    public function deleteEntityAction($id){
        if(!Loader::includeModule("gpi.workproject"))
            return;


        $addResult = Orm\TasksItemTable::delete($id);

        return json_encode([
            'status' => 1,
            'elemenId' => $addResult,
        ]);
    }

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS']){
            if($params['VARIABLES']['group_id'])
                $params['USER_PERMISSIONS'] = Entity\EditorManager::defineGroupPermission($params['VARIABLES']['group_id']);
            else
                $params['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($params['VARIABLES']['project_id']);
        }

        return $params;
    }

    function defineStructure(){
        $this->arResult['STRUCTURE'] = [
            [
                'CODE' => 'STATUS',
                'LABEL' => 'Статус',
            ],
            [
                'CODE' => 'PREORITY',
                'LABEL' => 'Приоритет',
            ],
            [
                'CODE' => 'CONTROL_DATE',
                'LABEL' => 'Крайний срок',
            ],
            [
                'CODE' => 'CREATED_TIME',
                'LABEL' => 'Создан',
            ],
            [
                'CODE' => 'PRODUCER',
                'LABEL' => 'Посановщик',
            ],
            [
                'CODE' => 'PROVIDER',
                'LABEL' => 'Исполнитель',
            ],
            [
                'CODE' => 'LABOR_COST',
                'LABEL' => 'Оценка трудозатрат исполнителя',
            ],
            [
                'CODE' => 'FILES',
                'LABEL' => 'Файлы',
            ],
        ];
    }

    function defineTask(){
        $this->arResult['TASK'] = Orm\TasksItemTable::getList([
            'select' => [
                '*',
                'PRODUCER_FULL_NAME',
                'PROVIDER_FULL_NAME',
                'PROVIDER_PREVIEW',
                'PRODUCER_PREVIEW',
                ],
            'filter' => ['ID' => $this->arParams['VARIABLES']['task_id']],
            'runtime' => [
                'PRODUCER_USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.PRODUCER' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'PRODUCER_USER_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'PRODUCER_USER.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PRODUCER_USER_NAME',
                    'COALESCE(%s, " ")',
                    'PRODUCER_USER.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PRODUCER_USER_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'PRODUCER_USER.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PRODUCER_FULL_NAME',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['PRODUCER_USER_LAST_NAME','PRODUCER_USER_NAME', 'PRODUCER_USER_SECOND_NAME']
                ),

                'PROVIDER_USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.PROVIDER' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'PROVIDER_USER_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'PROVIDER_USER.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PROVIDER_USER_NAME',
                    'COALESCE(%s, " ")',
                    'PROVIDER_USER.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PROVIDER_USER_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'PROVIDER_USER.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PROVIDER_FULL_NAME',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['PROVIDER_USER_LAST_NAME','PROVIDER_USER_NAME', 'PROVIDER_USER_SECOND_NAME']
                ),

                'PROVIDER_PREVIEW_FILE' => [
                    'data_type' => 'Bitrix\Main\FileTable',
                    'reference' => [
                        'this.PROVIDER_USER.PERSONAL_PHOTO' => 'ref.ID',
                    ]
                ],
                'PRODUCER_PREVIEW_FILE' => [
                    'data_type' => 'Bitrix\Main\FileTable',
                    'reference' => [
                        'this.PRODUCER_USER.PERSONAL_PHOTO' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'PROVIDER_PREVIEW',
                    'CONCAT("/upload/", %s, "/", %s)',
                    ['PROVIDER_PREVIEW_FILE.SUBDIR', 'PROVIDER_PREVIEW_FILE.FILE_NAME']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PRODUCER_PREVIEW',
                    'CONCAT("/upload/", %s, "/", %s)',
                    ['PRODUCER_PREVIEW_FILE.SUBDIR', 'PRODUCER_PREVIEW_FILE.FILE_NAME']
                ),
            ]
        ])->fetch();

        if(!$this->arResult['TASK']){
            global $USER;
            $this->arResult['TASK']['PRODUCER'] =$USER->getId();
            return;
        }

        foreach (array_filter($this->arResult['TASK'], fn($v) => $v instanceOf Bitrix\Main\Type\DateTime) as $dateKey => $timeObject)
            $this->arResult['TASK'][$dateKey] = $timeObject->format('d.m.Y H:i:s');

        $this->arResult['TASK']['STATUS'] = Orm\TasksItemTable::STATUS[$this->arResult['TASK']['STATUS']];
        $this->arResult['TASK']['PREORITY'] = Orm\TasksItemTable::PREORITY[$this->arResult['TASK']['PREORITY']];
        if($this->arResult['TASK']['FILES'])
            $this->arResult['TASK']['FILES'] = Bitrix\Main\FileTable::getList([
                'filter' => [
                    'ID' => $this->arResult['TASK']['FILES']
                ],
                'select' => [
                    'ID', 'TITLE' => 'ORIGINAL_NAME', 'LINK'
                ],
                'runtime' => [
                    new \Bitrix\Main\Entity\ExpressionField(
                        'LINK',
                        'CONCAT("/upload/", %s, "/", %s)',
                        ['SUBDIR', 'FILE_NAME']
                    ),
                ]
            ])->fetchAll();
    }


    function defineProjectGroups(){

        if(!Loader::includeModule("gpi.workproject"))
            return;

        $groupTable = new Orm\GroupItemTable();

        $groupsRS = $groupTable::getList([
            'select' => [
                'ID', 'TITLE'
            ],
            'filter' => [
                'UNION_ID' => $this->arParams['PROJECT']['GROUP_UNION_ID'],
            ],
            'runtime' => [
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'USER.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_NAME',
                    'COALESCE(%s, " ")',
                    'USER.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'USER.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'CREATOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['AUTHOR_LAST_NAME','AUTHOR_NAME', 'AUTHOR_SECOND_NAME']
                ),
            ]
        ]);

        while($group = $groupsRS->fetch()){
            $groups[$group['ID']]   = $group['TITLE'];
        }

        $this->arResult['PROJECT_GROUPS'] = $groups;

        global $APPLICATION;
        $APPLICATION->AddHeadString("
            <script> 
                const projectGroups = ".Bitrix\Main\Web\Json::encode($groups).";
            </script>
        ");
    }

    function defineProjectUserList(){
        $filter = [
            'PROJECT_ID' => $this->arParams['~VARIABLES']['project_id']
        ];
        if($this->arParams['~VARIABLES']['group_id'])
            $filter['GROUP_IDS.VALUE'] = $this->arParams['~VARIABLES']['group_id'];

        $usersRS = Orm\ProjectUserTable::getList([
            'filter' => $filter,
            'select' => ['FULL_NAME', 'USER_ID', 'GROUPS'],
            'runtime' => [
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.USER_ID' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'USER.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_NAME',
                    'COALESCE(%s, " ")',
                    'USER.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'USER.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'FULL_NAME',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['AUTHOR_LAST_NAME','AUTHOR_NAME', 'AUTHOR_SECOND_NAME']
                ),
                'GROUP_IDS' => [
                    'data_type' => 'Gpi\Workproject\Orm\ProjectUserGroupsTable',
                    'reference' => [
                        'this.ID' => 'ref.VALUE_ID'
                    ]
                ]
            ]
        ]);
        while($user = $usersRS->fetch()){
            if($this->arResult['TASK']['PROVIDER'] && $this->arResult['TASK']['PROVIDER'] == $user['USER_ID'])
                $this->arResult['PROVIDER_GROUPS'] = $user['GROUPS'];
            $users[] = [
                'id' => $user['USER_ID'],
                'entityId' => 'user',
                'title' => $user['FULL_NAME'],
                'tabs' => ['US_LIST'],
                'avatar' => implode(',', $user['GROUPS']),
            ];
        }

        global $APPLICATION;
        $APPLICATION->AddHeadString("
        <script> 
            const projectUsersList = ".Bitrix\Main\Web\Json::encode($users).";
        </script>
        ");
    }

    public function executeComponent() {
        $this->defineTask();
        $this->defineProjectUserList();
        $this->defineProjectGroups();
        $this->defineStructure();
        $this->IncludeComponentTemplate();
    }

}
