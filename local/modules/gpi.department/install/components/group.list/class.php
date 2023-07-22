<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity\UrlManager,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper;



class WorkprojectsProjectGroup extends  \CBitrixComponent implements Controllerable{

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($params['VARIABLES']['project_id']);

        return $params;
    }

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params){
        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:group.list",
            "",
            $params
        );

        return ob_get_clean();
    }

    function getMyAlowedGroupIds(){

        return array_column(Orm\GroupItemUserPermissionTable::getList([
            'filter' => [ 'ENTITY' => Entity\EditorManager::getCurrentUserPermissionEntities() ],
            'select' => ['GROUP_ID'],
            'group' => ['GROUP_ID'],
        ])->fetchAll(), 'GROUP_ID');
    }

    function getGroups(){

        if(!Loader::includeModule("gpi.workproject"))
            return;

        $groupTable = new Orm\GroupItemTable();

        $viewPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['group.list.item'];
        $editPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['group.list.item.edit'];

        $this->arResult['GROUPS'] = $groupTable::getList([
            'select' => [
                '*', 'CREATOR_FIO', 'LINK', 'EDIT_LINK'
            ],
            'filter' => [
                'UNION_ID' => $this->arParams['UNION_ID'],
                'ID' => $this->getMyAlowedGroupIds(),
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
        ])->fetchAll();
    }

    function defineParams(){


        CJSCore::Init(['sidepanel', 'ui.list', "ui.buttons"]);

        $this->arResult['PROJECT_PATH'] = UrlManager::getProjectItemLink($this->arParams['PROJECT_ID']);
        $this->arResult['CRATE_GROUP_PATH'] = UrlManager::getGroupItemEditLink($this->arParams['PROJECT_ID'], '0');
        $this->arResult['EDIT_USERS_PATH'] = UrlManager::getProjectUserListLink($this->arParams['PROJECT_ID']);
        $this->arResult['GRID_ID'] = 'workprojects_project_groups';
        $this->arResult['PERMISSIONS'] =  Entity\EditorManager::defineGroupListPermission(array_column($this->arResult['GROUPS'], 'ID'));

        global $APPLICATION;
        $APPLICATION->AddChainItem('Группы', UrlManager::getGroupListLink($this->arParams['PROJECT']['ID']));
    }

    public function definePermission(){

        global $USER;

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($this->arParams['VARIABLES']['project_id']);

        if(array_intersect(['R', 'W', 'X'], $this->arParams['USER_PERMISSIONS']))
            return true;

        header('Location: '.Entity\UrlManager::getProjectLockedLink($this->arParams['VARIABLES']['project_id']));
    }

    public function executeComponent() {

        if(!Loader::IncludeModule("gpi.workproject"))
            return;

        $this->definePermission();

        $this->getGroups();
        $this->defineParams();

        $this->IncludeComponentTemplate();
    }

}
