<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJSCore::Init(array("jquery","sidepanel","fx", 'ajax'));
use Bitrix\Main\Engine\Contract\Controllerable,
    \Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Orm;

class CalendarSettings extends  \CBitrixComponent implements Controllerable{

    protected $calendarTable = 'Gpi\Workproject\Orm\CalendarTable';
    protected $calendarUserTable = 'Gpi\Workproject\Orm\CalendarUserTable';
    protected $groupTable = 'Gpi\Workproject\Orm\GroupItemTable';

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineCalendarPermission($params['CALENDAR_ID']);


        return $params;
    }

    public function configureActions(){return[];}

    function defineCalendar(){

        if(!Loader::includeModule('gpi.workproject'))
            return false;

        if(!$this->arParams['CALENDAR_ID']){
            ShowMessage('Param "CALENDAR_ID" is required');
            return false;
        }

        $this->arResult['CALENDAR'] = $this->calendarTable::getById($this->arParams['CALENDAR_ID'])->fetch();
        if(!$this->arResult['CALENDAR']){
            ShowMessage('Mission calendar with "CALENDAR_ID" '.$this->arResult['CALENDAR_ID']);
            return false;
        }

        return true;
    }

    function defineRulls(){

        $this->arResult['RULLS'] = [

        ];
    }

    public function executeComponent() {
        if(!in_array('X', $this->arParams['USER_PERMISSIONS'])){
            header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
            return;
        }


        global $APPLICATION;
        $APPLICATION->setTitle('Настройка календаря');
        CJSCore::Init(array('date', 'sidepanel.reference.link.save', 'bear.file.input','rs.buttons', 'ui.entity-selector', 'ui.notification', 'ui.buttons', "ui.forms", 'ui.list'));


        if(!$this->defineCalendar())
            return;

        $this->includeComponentTemplate();
    }
}
