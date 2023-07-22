<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJSCore::Init(array("jquery","sidepanel","fx", 'ajax'));
use Bitrix\Main\Engine\Contract\Controllerable,
    \Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Orm;

class CalendsarRSEvent extends  \CBitrixComponent {

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

    function getEvent(){
        if(!Loader::includeModule("gpi.workproject"))
            return;

        global $USER;
        $userId = $USER->getId();

        $calendarEventTable =  new Orm\CalendarEventTable();

        $filter=[
            [
                'LOGIC' => 'OR',
                'PROVIDER' => $userId,
                'GUARANTOR' => $userId,
                'EXECUTOR' => $userId,
                'CREATED_BY' => $userId,
            ],
            'ID' => $this->arParams['VARIABLES']['event_id']
        ];

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

            foreach (array_filter($event, fn($v) => $v instanceof Bitrix\Main\Type\DateTime) as $code => $dataObj){
                $event[$code] = $dataObj->format('d.m.Y H:i:s');
            }

            $this->arResult['event'] = $event;
        }
    }

    public function definePermission(){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineCalendarPermission($this->arParams['CALENDAR_ID']);

        global $USER;

        if(
            $this->arResult['event']['CREATED_BY'] == $USER->getId() && array_intersect(['R'], $this->arParams['USER_PERMISSIONS'])
            ||
            array_intersect(['X'], $this->arParams['USER_PERMISSIONS'])
            ||
            !$this->arResult['event']['ID'] && array_intersect(['R'], $this->arParams['USER_PERMISSIONS'])
        )
            return true;


        header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
        return false;
    }

    public function executeComponent() {



        $this->defineUsers();
        $this->getEvent();
        $this->definePermission();




        \Bitrix\Main\UI\Extension::load(['ui.entity-selector', 'ui.notification', 'ui.buttons', "ui.forms"]);
        CJSCore::Init(array('date', 'sidepanel.reference.link.save', 'bear.file.input','rs.buttons'));

        $this->includeComponentTemplate();
    }
}
