<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper;

class CalendarRs extends  \CBitrixComponent  implements Controllerable{

    const dateFields = [
        'STARTED',
        'ENDED',
        'FACT_STARTED',
        'FACT_ENDED',
    ];

    public function configureActions(){
        return [];
    }
    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineCalendarPermission($params['CALENDAR_ID']);


        return $params;
    }

    public static function getEventsAction($arParams, $month, $year){
        \CBitrixComponent::IncludeComponentClass('rs:calendar.events');
        $calendarEntity = new CalendarRs();
        $calendarEntity->arParams = $arParams;
        $calendarEntity->arResult['year'] = $year;
        $calendarEntity->arResult['month'] = $month;
        return $calendarEntity->getEvents();
    }
    public static function loadEventAction()
    {
        if (!Loader::includeModule("gpi.workproject"))
            return;

        return Helper\FormData::save(new Orm\CalendarEventTable(), self::dateFields, 1);
    }
    
    public static function deleteEventAction($id, $arParams=[]){
        if (!Loader::includeModule("gpi.workproject"))
            return;

        \Gpi\Workproject\Orm\CalendarEventTable::delete($id);
    }

    public static function renameCalendarAction($id, $title){
        Orm\CalendarTable::update($id, ['TITLE' => $title]);
    }


    protected function addApplicationStrings(){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        global $APPLICATION;

        CJSCore::Init(array('date', 'bear.file.input', "sidepanel", 'ui.entity-selector', 'ui.notification', "ui.forms", 'boostrap', 'ui.buttons', 'ui.buttons.icons', 'ui.dialogs.messagebox','rs.buttons'));

        $this->arParams['path'] = $this->getPath();
        $css = [
            $this->arParams['path'].'/templates/.default/lib/scheduler-master/dhtmlxscheduler.css',
            $this->arParams['path'].'/templates/.default/lib/scheduler-master/dhtmlxscheduler_material.css',
        ];
        $js = [
            $this->arParams['path'].'/templates/.default/lib/calendar.js',
            $this->arParams['path'].'/templates/.default/lib/scheduler-master/dhtmlxscheduler.js'
        ];

        foreach ($css as $link){
            \Bitrix\Main\Page\Asset::getInstance()->addCss($link.'?preventChanche');
        }

        foreach ($js as $link){
            \Bitrix\Main\Page\Asset::getInstance()->addJs($link.'?preventChanche');
        }
    }
    function defineUsers(){
        $usersRS = Bitrix\Main\UserTable::getList([
            'filter' => [
                'ACTIVE' => 'Y'
            ],
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
            const usersCalendarList = ".json_encode($users).";
        </script>
        ");
    }

    function defineCookies(){
        global $USER;
        $userId = $USER->getId();
        $request = Application::getInstance()->getContext()->getRequest();

        $this->arResult['month']=$request->getCookieRaw('calendarMonth') ?? date('m');
        $this->arResult['year']=$request->getCookieRaw('calendarYear') ?? date('Y');
        $this->arResult['day']=$request->getCookieRaw('calendarDay') ?? date('d');
        $this->arResult['activeCalendarList']=$request->getCookieRaw('activeCalendarList') ?? 'N';
        $this->arResult['activeCalendarDay']=date('m-d-Y', strtotime($request->getCookieRaw('activeCalendarDay')));
        $this->arResult['calendarView']=$request->getCookieRaw('calendarView') ?? 'week';
        $this->arResult['user'] = $userId ?? 0;
    }

    function getEvents(){

        if(!Loader::includeModule("gpi.workproject"))
            return;

        $calendarEventTable = new Orm\CalendarEventTable();

        $filter=[
            [
                'LOGIC' => 'OD',
                'PROVIDER' => $userId,
                'GUARANTOR' => $userId,
                'EXECUTOR' => $userId,
                'CREATED_BY' => $userId,
            ]
        ];
        if($this->arResult['year'] && $this->arResult['month'])
            $filter = [
                '>STARTED' =>   \Bitrix\Main\Type\Datetime::createFromTimestamp(MakeTimeStamp(date('d.m.Y', strtotime('-1 MONTH', strtotime("20.".$this->arResult['month'].".".$this->arResult['year']))))),
                '<=ENDED' =>   \Bitrix\Main\Type\Datetime::createFromTimestamp(MakeTimeStamp(date('d.m.Y', strtotime('+1 MONTH', strtotime("10.".$this->arResult['month'].".".$this->arResult['year']))))),
            ];


        $filter[] =[
            'LOGIC' => 'OR',
            [
                '!CALENDAR_ID' => $this->arParams['CALENDAR_ID'],
                'IS_PUBLICK' => 1,
            ],
            [
                'CALENDAR_ID' => $this->arParams['CALENDAR_ID'],
            ]
        ];

        $eventsList = [];
        $fileIds=[];
        $eventsListRS = $calendarEventTable::getList([
            'filter' => $filter,
            'select' => ['*']
        ])->fetchAll();

        $filesArr = array_filter(array_column($eventsListRS, 'FILES'));
        foreach ($filesArr as $file){
            $fileIds = array_merge($fileIds , $file);
        }

        $filesListRS = Bitrix\Main\FileTable::getList(['filter' => ['ID' => $fileIds]]);
        while($file = $filesListRS->fetch()){
            $file['SRC'] = '/upload/'.$file['SUBDIR'].'/'.$file['FILE_NAME'];
            $filesList[$file['ID']] = $file;
        }

        foreach($eventsListRS as $event){
            $event['FILES'] = array_map(function($v) use($filesList){
                return $filesList[$v];
            }, $event['FILES']);

            $extended = $event;

            foreach (array_filter($event, fn($v) => $v instanceof Bitrix\Main\Type\DateTime) as $code => $dataObj){
                $event[$code] = $dataObj->format('Y-m-d H:i:s');
                $extended[$code] = $dataObj->format('d.m.Y H:i:s');
            }

            $eventsList[] = [
                'text'=>$event['TITLE'],
                'start_date'=> $event['STARTED'],
                'end_date'=>$event['ENDED'],
                'id'=>$event['ID'],
                'editable' => true,
                'extendedProps' => $extended,
                'resourceEditable' => true,
                "color" => "#0fa5a7"
            ];
        }



        global $APPLICATION;
        $APPLICATION->AddHeadString("
        <script> 
            const eventsList = ".CUtil::PhpToJSObject($eventsList).";
            const userId = ".$this->arResult['user'].";
            const activeCalendarList = '". $this->arResult['activeCalendarList']."';
            const activeCalendarDay = '".$this->arResult['activeCalendarDay']."';
            const calendarView = '". $this->arResult['calendarView']."';
        </script>
        ");

        return $eventsList;
    }

    function defineCalendar(){

        $this->arResult['CALENDAR'] = Orm\CalendarTable::getById($this->arParams['CALENDAR_ID'])->fetch();

        global $APPLICATION;
        $APPLICATION->AddHeadString("
        <script> 
            const calendarName = '".$this->arResult['CALENDAR']['TITLE']."';
            const calendarId = '".$this->arParams['CALENDAR_ID']."';
        </script>
        ");

        return true;
    }

    public function definePermission(){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineCalendarPermission($this->arParams['CALENDAR_ID']);

        global $USER;

        if(array_intersect(['R', 'W', 'X'], $this->arParams['USER_PERMISSIONS']) )
            return true;


        header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
    }


    public function executeComponent() {

        $this->definePermission();

        $this->addApplicationStrings();

        $this->defineUsers();
        $this->defineCookies();
        $this->getEvents();
        $this->defineCalendar();

        global $APPLICATION;
        $APPLICATION->AddChainItem('Календарь', $this->arParams['SEF_FOLDER']);

        $this->includeComponentTemplate();
    }
}
