<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Entity\UrlManager,
    Gpi\Workproject\Orm;

class ProjectEntitySettings extends  \CBitrixComponent implements Controllerable{

    protected $projectTable = 'Gpi\Workproject\Orm\ProjectTable';
    protected $projectDirectionTable = 'Gpi\Workproject\Orm\ProjectDirectionTable';
    protected $groupItemTable = 'Gpi\Workproject\Orm\GroupItemTable';
    protected $projectUserTable = 'Gpi\Workproject\Orm\ProjectUserTable';
    protected $projectUserGroupsTable = 'Gpi\Workproject\Orm\ProjectUserGroupsTable';

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params){
        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:project.entity.structure",
            "",
            $params
        );

        return ob_get_clean();
    }


    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($params['VARIABLES']['project_id']);

        return $params;
    }


    function defineProject(){

        $this->arResult['PROJECT'] = $this->projectTable::getList([
            'select' => ['ID', 'TITLE', 'CREATOR_FIO', 'DIRECTOR_FIO', 'GROUP_UNION_ID', 'DIRECTOR_ID'],
            'filter' => ['ID' => $this->arParams['VARIABLES']['project_id']],
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
                'USER_2' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.DIRECTOR_ID' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'USER_2.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_NAME',
                    'COALESCE(%s, " ")',
                    'USER_2.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'USER_2.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['DIRECTOR_LAST_NAME','DIRECTOR_NAME', 'DIRECTOR_SECOND_NAME']
                ),
            ]
        ])->fetch();

    }

    function defineProjectDirections(){
        $directionsRS = $this->projectDirectionTable::getList([
            'filter' => ['PROJECT_ID' => $this->arParams['VARIABLES']['project_id']],
            'select' => ['ID', 'TITLE', 'CREATOR_FIO', 'DIRECTOR_FIO', 'DIRECTOR_ID'],
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
                'USER_2' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.DIRECTOR_ID' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'USER_2.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_NAME',
                    'COALESCE(%s, " ")',
                    'USER_2.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'USER_2.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['DIRECTOR_LAST_NAME','DIRECTOR_NAME', 'DIRECTOR_SECOND_NAME']
                ),
            ]
        ]);

        while($direction = $directionsRS->fetch()){
            $this->arResult['DIRECTIONS'][$direction['ID']] = $direction;
            $this->arResult['DIRECTION_DIRECTORS'][$direction['DIRECTOR_ID']] = $direction['DIRECTOR_ID'];
        }
    }

    function defineGroups(){

        $filter = ['UNION_ID' => $this->arResult['PROJECT']['GROUP_UNION_ID']];

        if($this->arParams['VARIABLES']['group_id'])
            $filter['ID'] = $this->arParams['VARIABLES']['group_id'];

        $viewPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['group.list.item'];
        $editPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['group.list.item.edit'];

        $groupsRS = $this->groupItemTable::getList([
            'filter' => $filter,
            'select' => ['ID', 'TITLE', 'CREATOR_FIO', 'DIRECTOR_FIO', 'DIRECTION', 'DIRECTOR_ID', 'EDIT_LINK', 'LINK'],
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
                'USER_2' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.DIRECTOR_ID' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'USER_2.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_NAME',
                    'COALESCE(%s, " ")',
                    'USER_2.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'USER_2.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'DIRECTOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['DIRECTOR_LAST_NAME','DIRECTOR_NAME', 'DIRECTOR_SECOND_NAME']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_RS',
                    'REPLACE("'.$viewPath.'", "#project_id#", "'.$this->arParams['PROJECT_ID'].'")',
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK',
                    'REPLACE(%s, "#group_id#", %s)',
                    ['LINK_RS', 'ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK_RS',
                    'REPLACE("'.$editPath.'", "#project_id#", "'.$this->arParams['PROJECT_ID'].'")',
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE(%s, "#group_id#", %s)',
                    ['EDIT_LINK_RS', 'ID']
                ),
            ]
        ]);

        while($group = $groupsRS->fetch()){
            $this->arResult['DIRECTIONS'][$group['DIRECTION']]['GROUPS'][$group['ID']] = $group;
            $this->arResult['GROUPS'][$group['ID']] = $group;
            $this->arResult['GROUP_DIRECTORS'][$group['DIRECTOR_ID']] = $group['DIRECTOR_ID'];
        }

        $this->arResult['GROUP_PERMISSIONS'] =  Entity\EditorManager::defineGroupListPermission(array_column($this->arResult['GROUPS'], 'ID'));
    }

    function defineGroupUsers(){
        $categoriesDescs = Orm\ProjectUserTable::CATEGORY_SHORT;

        $userRS = $this->projectUserTable::getList([
            'filter' => ['PROJECT_ID' => $this->arResult['PROJECT']['ID']],
            'select' => ['USER_FIO', 'USER_ID', 'GROUPS', 'CATEGORY'],
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
                    'USER_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['AUTHOR_LAST_NAME','AUTHOR_NAME', 'AUTHOR_SECOND_NAME']
                ),
            ]
        ]);

        while($user = $userRS->fetch()){

            /*
            if($this->arResult['PROJECT']['DIRECTOR_ID'] == $user['USER_ID'])
                $user['CATEGORY'][] = 'РП';
            if($this->arResult['DIRECTION_DIRECTORS'][$user['USER_ID']])
                $user['CATEGORY'][] = 'РПН';
            if($this->arResult['GROUP_DIRECTORS'][$user['USER_ID']])
                $user['CATEGORY'][] = 'РГ';
            if(!$user['CATEGORY'])
                $user['CATEGORY'][] = 'Участник';
            */

            $user['CATEGORY'] = array_map(function($v) use ($categoriesDescs){
                return $categoriesDescs[$v];
            }, $user['CATEGORY']);

            $user['CATEGORY'] = implode(', ', $user['CATEGORY']);

            foreach ($user['GROUPS'] as $groupId){
                if($this->arResult['DIRECTIONS'][$this->arResult['GROUPS'][$groupId]['DIRECTION']]['GROUPS'][$groupId])
                    $this->arResult['DIRECTIONS'][$this->arResult['GROUPS'][$groupId]['DIRECTION']]['GROUPS'][$groupId]['USERS'][] = $user;
            }
        }

        $this->arResult['DIRECTIONS'] = array_filter($this->arResult['DIRECTIONS'], fn($v) => $v['GROUPS']);
    }

    function defineProps(){
        global $APPLICATION;
        $APPLICATION->AddHeadString("
        <script> 
            window.projectEntityStructureParams = ".Bitrix\Main\Web\Json::encode($this->arParams).";
        </script>
        ");
    }

    public function executeComponent() {

        $this->defineProject();
        $this->defineProjectDirections();
        $this->defineGroups();
        $this->defineGroupUsers();
        $this->defineProps();

        $this->IncludeComponentTemplate();
    }
}
