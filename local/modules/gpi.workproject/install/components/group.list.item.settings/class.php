<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Contract\Controllerable,
    \Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Orm;

class GroupSettings extends  \CBitrixComponent implements Controllerable{

    protected $GroupTable = 'Gpi\Workproject\Orm\GroupItemTable';

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineGroupPermission($params['VARIABLES']['group_id']);


        return $params;
    }

    public function configureActions(){return[];}

    function defineGroup(){

        if(!Loader::includeModule('gpi.workproject'))
            return false;

        if(!$this->arParams['VARIABLES']['group_id']){
            ShowMessage('Param "GROUP_ID" is required');
            return false;
        }

        $this->arResult['GROUP'] = $this->GroupTable::getById($this->arParams['VARIABLES']['group_id'])->fetch();

        if(!$this->arResult['GROUP']){
            ShowMessage('Mission forum with "GROUP_ID" '.$this->arResult['GROUP_ID']);
            return false;
        }

        return true;
    }

    public function checkPermission(){

        global $USER;

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineGroupPermission($this->arParams['VARIABLES']['group_id']);

        if(array_intersect(['X'], $this->arParams['USER_PERMISSIONS']) )
            return true;

        header('Location: '.Entity\UrlManager::getGroupLockedLink($this->arParams['VARIABLES']['project_id'], $this->arParams['VARIABLES']['group_id']));
    }

    function defineUserList($filter=['ACTIVE' => 'Y']){
        $usersRS = Bitrix\Main\UserTable::getList([
            'filter' => $filter,
            'select' => ['FULL_NAME', 'ID'],
            'runtime' => [
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_NAME',
                    'COALESCE(%s, " ")',
                    'NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'FULL_NAME',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['AUTHOR_LAST_NAME','AUTHOR_NAME', 'AUTHOR_SECOND_NAME']
                ),
            ]
        ]);
        while($user = $usersRS->fetch()){
            $users[] = [
                'id' => $user['ID'],
                'entityId' => 'user',
                'title' => $user['FULL_NAME'],
                'tabs' => ['US_LIST'],
            ];
        }

        global $APPLICATION;
        $APPLICATION->AddHeadString("
        <script> 
            const groupUsersList = ".Bitrix\Main\Web\Json::encode($users).";
        </script>
        ");
    }

    public function executeComponent() {

        $this->checkPermission();

        global $APPLICATION;
        $APPLICATION->setTitle('Настройка группы');
        CJSCore::Init(array('date', 'sidepanel.reference.link.save', 'bear.file.input','rs.buttons', 'ui.entity-selector', 'ui.notification', 'ui.buttons', "ui.forms", 'ui.list'));

        $this->defineUserList();

        if(!$this->defineGroup())
            return;

        $this->includeComponentTemplate();
    }
}
