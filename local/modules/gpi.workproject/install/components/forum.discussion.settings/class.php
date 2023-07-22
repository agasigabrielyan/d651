<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Contract\Controllerable,
    \Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Orm;

class ForumDiscussionSettings extends  \CBitrixComponent implements Controllerable{

    protected $ForumTable = 'Gpi\Workproject\Orm\ForumTable';

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineForumPermission($params['FORUM_ID']);


        return $params;
    }

    public function configureActions(){return[];}

    function defineForum(){

        if(!Loader::includeModule('gpi.workproject'))
            return false;

        if(!$this->arParams['FORUM_ID']){
            ShowMessage('Param "FORUM_ID" is required');
            return false;
        }

        $this->arResult['FORUM'] = $this->ForumTable::getById($this->arParams['FORUM_ID'])->fetch();

        if(!$this->arResult['FORUM']){
            ShowMessage('Mission forum with "FORUM_ID" '.$this->arResult['FORUM_ID']);
            return false;
        }

        return true;
    }

    public function checkPermission(){

        global $USER;

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineForumPermission($this->arParams['FORUM_ID']);

        if(array_intersect(['X'], $this->arParams['USER_PERMISSIONS']) )
            return true;

        header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
    }

    public function executeComponent() {

        $this->checkPermission();

        global $APPLICATION;
        $APPLICATION->setTitle('Настройка форума');
        CJSCore::Init(array('date', 'sidepanel.reference.link.save', 'bear.file.input','rs.buttons', 'ui.entity-selector', 'ui.notification', 'ui.buttons', "ui.forms", 'ui.list'));


        if(!$this->defineForum())
            return;

        $this->includeComponentTemplate();
    }
}
