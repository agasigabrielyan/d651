<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Entity\UrlManager;


class RSDriveSettings extends  \CBitrixComponent implements Controllerable{

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineStoragePermission($params['STORAGE_ID']);


        return $params;
    }

    public function configureActions(){return[];}

    function definetStorage(){

        if(!Loader::includeModule('gpi.workproject'))
            return false;

        if(!$this->arParams['STORAGE_ID']){
            ShowMessage('Param "STORAGE_ID" is required');
            return false;
        }

        $this->arResult['STORAGE'] = Orm\StorageTable::getById($this->arParams['STORAGE_ID'])->fetch();
        if(!$this->arResult['STORAGE']){
            ShowMessage('Mission storage with "STORAGE_ID" '.$this->arResult['STORAGE_ID']);
            return false;
        }

        return true;
    }

    public function executeComponent() {
        if(!in_array('X', $this->arParams['USER_PERMISSIONS'])){
            header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
            return;
        }


        global $APPLICATION;
        $APPLICATION->setTitle('Настройка диска');
        CJSCore::Init(array('date', 'sidepanel.reference.link.save', 'bear.file.input','rs.buttons', 'ui.entity-selector', 'ui.notification', 'ui.buttons', "ui.forms", 'ui.list'));


        if(!$this->definetStorage())
            return;

        $this->includeComponentTemplate();
    }

}
