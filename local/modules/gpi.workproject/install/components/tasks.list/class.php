<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Bitrix\Main\Engine\Contract\Controllerable;


class RsTasksList extends  \CBitrixComponent implements Controllerable{

    function configureActions(){}

    public static function getComponentTemplateResultAction($params){
        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:tasks.list",
            "",
            $params
        );

        return ob_get_clean();
    }

    public static function deleteEntityAction($id){
        if(!Loader::IncludeModule('gpi.workproject'))
            return;

        return Orm\TasksItemTable::delete($id)->isSuccess();
    }

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if($params['USER_PERMISSIONS']){
            if($params['~VARIABLES']['group_id'])
                $params['USER_PERMISSIONS'] = Entity\EditorManager::defineGroupPermission($params['~VARIABLES']['group_id']);
            else
                $params['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($params['~VARIABLES']['project_id']);
        }

        return $params;
    }

    function definePermission(){

        if(array_intersect(['R', 'W', 'X'], $this->arParams['USER_PERMISSIONS']) )
            return true;

        //header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
    }

    function defineProps(){

        if($this->arParams['~VARIABLES']['group_id'])
            $this->arResult['CREATE_TASK_LINK']  = Entity\UrlManager::getGroupTasksListItemEditLink($this->arParams['~VARIABLES']['project_id'], $this->arParams['~VARIABLES']['group_id'], 0);
        else
            $this->arResult['CREATE_TASK_LINK']  = Entity\UrlManager::getProjectTasksListItemEditLink($this->arParams['~VARIABLES']['project_id'], 0);

        $this->arResult['FILTER_ID'] = 'rs_task_list'.$this->arParams['~VARIABLES']['project_id'].$this->arParams['~VARIABLES']['group_id'];

        $this->arResult['DELETE_BTN'] = Entity\EditorManager::DELETE_SVG;
        $this->arResult['EDIT_BTN'] = Entity\EditorManager::EDIT_SVG;

        CJSCore::init(['sidepanel', 'ui.list', 'ui.entity-selector']);

        global $APPLICATION;
        $APPLICATION->AddHeadString("
        <script> 
            const tasksListParams = ".Bitrix\Main\Web\Json::encode($this->arParams).";
        </script>
        ");
    }

    function defineFilterSet(){
        $this->arResult['FILTER'] = [
            'TITLE' =>  [
                'id' => 'TITLE',
                'name' => 'Название',
                'default' => true,
                'type' => 'text',
            ],
            'PRODUCER' =>  [
                'id' => 'PRODUCER',
                'name' => 'Постановщик',
                'default' => true,
                "type" => "custom_entity",
            ],
            'PROVIDER' =>  [
                'id' => 'PROVIDER',
                'name' => 'Исполнитель',
                'default' => true,
                "type" => "custom_entity",
            ],
            'CREATED_TIME' =>  [
                'id' => 'CREATED_TIME',
                'name' => 'Дата создания',
                'default' => true,
                'type' => 'date',
            ],
            'CONTROL_DATE' =>  [
                'id' => 'CONTROL_DATE',
                'name' => 'Контрольный срок',
                'default' => true,
                'type' => 'date',
            ],
            'PREORITY' =>  [
                'id' => 'PREORITY',
                'name' => 'Приоритет',
                'default' => true,
                'type' => 'list',
                'items' => array_merge(['' => 'Не выбран'], Orm\TasksItemTable::PREORITY)
            ],
            'STATUS' =>  [
                'id' => 'STATUS',
                'name' => 'Статус',
                'default' => true,
                'type' => 'list',
                'items' => array_merge(['' => 'Не выбран'], Orm\TasksItemTable::STATUS)
            ],
        ];
    }

    function defineProjectUserList(){
        $filter = [
            'PROJECT_ID' => $this->arParams['~VARIABLES']['project_id'],
        ];
        if($this->arParams['~VARIABLES']['group_id'])
            $filter['GROUP_IDS.VALUE'] = $this->arParams['~VARIABLES']['group_id'];

        $usersRS = Orm\ProjectUserTable::getList([
            'filter' => $filter,
            'select' => ['FULL_NAME', 'USER_ID'],
            'runtime' => [
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.USER_ID' => 'ref.ID',
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
                    'FULL_NAME',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['AUTHOR_LAST_NAME','AUTHOR_NAME', 'AUTHOR_SECOND_NAME']
                ),
                'GROUP_IDS' => [
                    'data_type' => 'Gpi\Workproject\Orm\ProjectUserGroupsTable',
                    'reference' => [
                        'this.ID' => 'ref.VALUE_ID'
                    ]
                ]
            ]
        ]);
        while($user = $usersRS->fetch()){
            $users[] = [
                'id' => $user['USER_ID'],
                'entityId' => 'user',
                'title' => $user['FULL_NAME'],
                'tabs' => ['US_LIST'],
            ];
        }

        global $APPLICATION;
        $APPLICATION->AddHeadString("
        <script> 
            const projectUsersList = ".Bitrix\Main\Web\Json::encode($users).";
        </script>
        ");
    }

    function getFilter(){
        global $USER;
        $userId = $USER->getId();

        $filter = [
            'PROJECT_ID' => $this->arParams['~VARIABLES']['project_id'],
            [
                'LOGIC' => 'OR',
                ['CREATED_BY' => $userId],
                ['PRODUCER' => $userId,],
                ['APPROVE.RP_ID' => $userId],
                ['APPROVE.RPN_ID' => $userId],
                ['APPROVE.RG_ID' => $userId],
                [
                    'PROVIDER' => $userId,
                    'APPROVE.RP_APPROVED' => 'Y',
                    'APPROVE.RPN_APPROVED' => 'Y',
                    'APPROVE.RG_APPROVED' => 'Y',
                ],
            ]
        ];

        if($this->arParams['~VARIABLES']['group_id'])
            $filter['GROUP_ID'] = $this->arParams['~VARIABLES']['group_id'];

        $filterOption = new \Bitrix\Main\UI\Filter\Options($this->arResult['FILTER_ID']);
        $filterData = $filterOption->getFilter([]);

        foreach ($filterData as $key => $value) {
            if(!$value || $value=='undefined')
                continue;

            if($key == 'FIND' || $key == 'TITLE'){
                $filter['TITLE'] = "%$value%";
                continue;
            }

            if($this->arResult['FILTER'][$key]){
                $filter[$key] = $value;
            }
            $exp = explode('_', $key);

            $mirrorName='';

            if ($exp[count($exp) - 1] == 'from') {
                $additProp = '>=';
                $mirrorName = mb_substr($key, 0, strlen($key) - 5);

            } else if ($exp[count($exp) - 1] == 'to') {
                $additProp = '<=';
                $mirrorName = mb_substr($key, 0, strlen($key) - 3);
            }

            if($this->arResult['FILTER'][$mirrorName]){
                $filter[$additProp.$mirrorName] = $value;
            }
        }

        return $filter;
    }

    function defineTaks(){
        $tasksRS = Orm\TasksItemTable::getList([
            'select' => [
                'PRODUCER_FULL_NAME',
                'PROVIDER_FULL_NAME',
                'ID',
                'PROVIDER',
                'PRODUCER',
                'TITLE',
                'CREATED_TIME',
                'STATUS',
                'PREORITY'
            ],
            'filter' => $this->getFilter(),
            'runtime' => [
                'APPROVE' => [
                    'data_type' => 'Gpi\Workproject\Orm\TasksItemApprovalTable',
                    'reference' => [
                      'this.ID' => 'ref.TASK_ID',
                    ],
                ],
                'PRODUCER_USER' => [
                  'data_type' => 'Bitrix\Main\UserTable',
                  'reference' => [
                      'this.PRODUCER' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'PRODUCER_USER_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'PRODUCER_USER.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PRODUCER_USER_NAME',
                    'COALESCE(%s, " ")',
                    'PRODUCER_USER.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PRODUCER_USER_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'PRODUCER_USER.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PRODUCER_FULL_NAME',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['PRODUCER_USER_LAST_NAME','PRODUCER_USER_NAME', 'PRODUCER_USER_SECOND_NAME']
                ),

                'PROVIDER_USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.PROVIDER' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'PROVIDER_USER_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'PROVIDER_USER.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PROVIDER_USER_NAME',
                    'COALESCE(%s, " ")',
                    'PROVIDER_USER.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PROVIDER_USER_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'PROVIDER_USER.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'PROVIDER_FULL_NAME',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['PROVIDER_USER_LAST_NAME','PROVIDER_USER_NAME', 'PROVIDER_USER_SECOND_NAME']
                ),
            ]
        ]);

        while($task = $tasksRS->fetch()){
            foreach (array_filter($task, fn($v) => $v instanceOf Bitrix\Main\Type\DateTime) as $dateKey => $timeObject)
                $task[$dateKey] = $timeObject->format('d.m.Y H:i:s');
            
            $task['STATUS'] = Orm\TasksItemTable::STATUS[$task['STATUS']];
            $task['PREORITY'] = Orm\TasksItemTable::PREORITY[$task['PREORITY']];

            $this->arResult['TASKS'][] = $task;
        }
    }

    public function executeComponent() {

        $this->definePermission();
        $this->defineProps();
        $this->defineFilterSet();
        $this->defineProjectUserList();

        $this->defineTaks();

        $this->IncludeComponentTemplate();
    }

}
