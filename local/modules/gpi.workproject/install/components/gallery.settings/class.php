<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Contract\Controllerable,
    \Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Orm;

class ForumDiscussionSettings extends  \CBitrixComponent implements Controllerable{

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineGalleryPermission($params['GALLERY_ID']);


        return $params;
    }

    public function configureActions(){return[];}

    function defineGallery(){

        if(!Loader::includeModule('gpi.workproject'))
            return false;

        if(!$this->arParams['GALLERY_ID']){
            ShowMessage('Param "GALLERY_ID" is required');
            return false;
        }

        $this->arResult['GALLERY'] = Orm\GalleryTable::getById($this->arParams['GALLERY_ID'])->fetch();

        if(!$this->arResult['GALLERY']){
            ShowMessage('Mission gallery with "GALLERY_ID" '.$this->arResult['GALLERY_ID']);
            return false;
        }

        return true;
    }

    public function checkPermission(){

        global $USER;

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineGalleryPermission($this->arParams['GALLERY_ID']);

        if(array_intersect(['X'], $this->arParams['USER_PERMISSIONS']) )
            return true;

        header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
    }

    public function executeComponent() {

        $this->checkPermission();

        global $APPLICATION;
        $APPLICATION->setTitle('Настройка галереи');
        CJSCore::Init(array('date', 'sidepanel.reference.link.save', 'bear.file.input','rs.buttons', 'ui.entity-selector', 'ui.notification', 'ui.buttons', "ui.forms", 'ui.list'));


        if(!$this->defineGallery())
            return;

        $this->includeComponentTemplate();
    }
}
