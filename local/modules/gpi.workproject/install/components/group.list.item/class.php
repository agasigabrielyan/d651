<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
    Bitrix\Crm\Service\Container,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Entity\UrlManager,
    Gpi\Workproject\Helper;


class WorkprojectsItemGroup extends  \CBitrixComponent implements Controllerable{

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineGroupPermission($params['VARIABLES']['group_id']);

        return $params;
    }

    function configureActions(){
        return[];
    }

    public static function renameGroupAction($id, $title, $directorId){
        Orm\GroupItemTable::update($id, ['TITLE' => $title, 'DIRECTOR_ID' => $directorId]);
    }

    function defineGroupData(){
        $projectTable = new Orm\ProjectTable();
        $groupTable = new Orm\GroupItemTable();

        $this->arResult['PROJECT'] = $projectTable::getList([
            'select' => ['*', 'CREATOR_FIO'],
            'filter' => ['ID' => $this->arParams['PROJECT_ID']],
            'runtime' => [
                'creator' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
                    ]
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'CREATOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['creator.LAST_NAME','creator.NAME', 'creator.SECOND_NAME']
                )
            ]
        ])->fetch();

        if($this->arParams['GROUP_ID']){
            $viewPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['group.list.item'];
            $editPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['group.list.item.edit'];
            $this->arResult['GROUP'] = $groupTable::getList([
                'select' => ['*', 'CREATOR_FIO', 'EDIT_LINK', 'LINK'],
                'filter' => ['ID' => $this->arParams['GROUP_ID']],
                'runtime' => [
                    'creator' => [
                        'data_type' => 'Bitrix\Main\UserTable',
                        'reference' => [
                            'this.CREATED_BY' => 'ref.ID',
                        ]
                    ],
                    new Bitrix\Main\Entity\ExpressionField(
                        'CREATOR_FIO',
                        'CONCAT(%s, " ", %s, " ", %s)',
                        ['creator.LAST_NAME','creator.NAME', 'creator.SECOND_NAME']
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
            ])->fetch();
        }

    }

    public function definePermission(){

        global $USER;

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineGroupPermission($this->arParams['VARIABLES']['group_id']);

        if($this->arParams['VARIABLES']['group_id'] == 0)
            return true;

        if($this->arParams['COMPONENT_PAGE'] == 'group.list.item.edit'
            &&
            !(
                array_intersect(['X'], $this->arParams['USER_PERMISSIONS'])
                ||
                (array_intersect(['W'], $this->arParams['USER_PERMISSIONS']) && $this->arParams['CREATED_BY'] == $USER->getId())
            )
        )
            header('Location: '.Entity\UrlManager::getGroupLockedLink($this->arParams['VARIABLES']['project_id'], $this->arParams['VARIABLES']['group_id']));

        if(array_intersect(['R', 'W', 'X'], $this->arParams['USER_PERMISSIONS']) )
            return true;

        header('Location: '.Entity\UrlManager::getGroupLockedLink($this->arParams['VARIABLES']['project_id'], $this->arParams['VARIABLES']['group_id']));
    }

    function defineParams(){
                global $APPLICATION;
        CJSCore::init(["sidepanel"]);
                $APPLICATION->setTitle('Группа: '.$this->arResult['GROUP']['TITLE']);
        $this->arResult['DISCUSSION_PATH'] = UrlManager::getGroupDiscussionListLink($this->arParams['VARIABLES']['project_id'], $this->arParams['VARIABLES']['group_id']);
        $this->arResult['GROUP_PATH'] = UrlManager::getGroupItemLink($this->arParams['VARIABLES']['project_id'], $this->arParams['VARIABLES']['group_id']);
        $this->arResult['SETTINGS_PATH'] = $this->arResult['GROUP_PATH'].'settings/';
    }

    
    public function executeComponent() {

        $this->definePermission();
        $this->defineParams();
        $this->defineGroupData();


        $this->IncludeComponentTemplate();
    }

}
