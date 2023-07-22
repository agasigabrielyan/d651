<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJSCore::Init(array("jquery","sidepanel","fx", 'ajax'));
use Bitrix\Main\Engine\Contract\Controllerable,
    \Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Orm;

class AdUnonSettingsSettings extends  \CBitrixComponent implements Controllerable{

    protected $AdUnonTable = 'Gpi\Workproject\Orm\AdUnionTable';
    protected $AdUnonUserTable = 'Gpi\Workproject\Orm\AdUnionUserTable';
    protected $AdItemTable = 'Gpi\Workproject\Orm\AdItemTable';

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineAdPermission($params['UNION_ID']);


        return $params;
    }

    public function configureActions(){return[];}

    function defineUnion(){

        if(!Loader::includeModule('gpi.workproject'))
            return false;

        if(!$this->arParams['UNION_ID']){
            ShowMessage('Param "UNION_ID" is required');
            return false;
        }

        $this->arResult['UNION'] = $this->AdUnonTable::getById($this->arParams['UNION_ID'])->fetch();

        if(!$this->arResult['UNION']){
            ShowMessage('Mission union with "UNION_ID" '.$this->arResult['UNION_ID']);
            return false;
        }

        return true;
    }

    function defineRulls(){

        $this->arResult['RULLS'] = [

        ];
    }

    public function checkPermission(){

        global $USER;

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineAdPermission($this->arParams['UNION_ID']);

        if(array_intersect(['X'], $this->arParams['USER_PERMISSIONS']) )
            return true;

        header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
    }

    public function executeComponent() {
        $this->checkPermission();


        global $APPLICATION;
        $APPLICATION->setTitle('Настройка событий');
        CJSCore::Init(array('date', 'sidepanel.reference.link.save', 'bear.file.input','rs.buttons', 'ui.entity-selector', 'ui.notification', 'ui.buttons', "ui.forms", 'ui.list'));


        if(!$this->defineUnion())
            return;

        $this->includeComponentTemplate();
    }
}
