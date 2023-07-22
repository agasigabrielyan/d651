<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Contract\Controllerable,
    \Bitrix\Main\Application,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Orm;

class RsEntityUserPermissionConfigurator extends  \CBitrixComponent implements Controllerable{

    protected $groupTable = 'Gpi\Workproject\Orm\GroupItemTable';

    public function configureActions(){return[];}

    public static function loadItemsAction($params, $loadItems=[], $deleteIds=[]){
        foreach ($params['MODULES_LIST'] as $moduleName)
            if(!Loader::includeModule($moduleName))
                return false;

        foreach ($loadItems as $loadItem){
            $id = $loadItem['ID'];
            unset($loadItem['ID'], $loadItem['ENTITY_S'], $loadItem['EDITED']);
            if(strpos($id, 'TEMPLATE') === false)
                $params['TABLE_NAME']::update($id, $loadItem);
            else
                $params['TABLE_NAME']::add($loadItem);
        }

        foreach ($deleteIds as $id){
            $params['TABLE_NAME']::delete($id);
        }
    }

    function getUsers($filter=[]){
        $userList=[];
        $userListRS = \Bitrix\Main\UserTable::getList([
                'filter' => $filter,
                'select' => ['ID', 'TITLE'],
                'runtime' => [
                    new \Bitrix\Main\Entity\ExpressionField(
                        'COAL_LAST_NAME',
                        'COALESCE(%s, " ")',
                        'LAST_NAME'
                    ),
                    new \Bitrix\Main\Entity\ExpressionField(
                        'COAL_NAME',
                        'COALESCE(%s, " ")',
                        'NAME'
                    ),
                    new \Bitrix\Main\Entity\ExpressionField(
                        'COAL_SECOND_NAME',
                        'COALESCE(%s, " ")',
                        'SECOND_NAME'
                    ),
                    new \Bitrix\Main\Entity\ExpressionField(
                        'TITLE',
                        'CONCAT(%s, " ", %s, " ", %s)',
                        ['COAL_LAST_NAME','COAL_NAME', 'COAL_SECOND_NAME']
                    ),
                ]
            ]
        );
        while($user = $userListRS->fetch()){
            $userList['U_'.$user['ID']] = $user['TITLE'];
        }

        return $userList;
    }

    function getGroupsList($filter=[]){
        $groupListRS = Bitrix\Main\GroupTable::getList([
            'filter' => ['ACTIVE' => 'Y'],
            'select' => ['NAME', 'ID'],
        ]);
        while($group = $groupListRS->fetch()){
            $groups['G_'.$group['ID']] = $group['NAME'];
        }
        return $groups;
    }

    function getProjectGroups($filter=[]){
        $filter['ID'] = array_column(Orm\GroupItemUserPermissionTable::getList([
            'filter' => [ 'ENTITY' => Entity\EditorManager::getCurrentUserPermissionEntities() ],
            'select' => ['GROUP_ID'],
            'group' => ['GROUP_ID'],
        ])->fetchAll(), 'GROUP_ID');

        if($this->arParams['PROJECT_GROUP_EXISTS_GROUUP_ID']){
            $filter['UNION_ID'] = $this->arParams['PROJECT_GROUP_EXISTS_GROUUP_ID'];
        }
        
        $projGroups = [];
        $projGroupListRS = $this->groupTable::getList([
            'filter' => $filter,
            'select' => ['TITLE', 'ID'],
        ]);
        while($projGroup = $projGroupListRS->fetch()){
            $projGroups['PG_'.$projGroup['ID']] = $projGroup['TITLE'];
        }
        return $projGroups;
    }

    function defineList(){

        $params = $this->arParams;

        foreach ($params['MODULES_LIST'] as $moduleName)
            if(!Loader::includeModule($moduleName))
                return false;

        $filter = [];
        if($params['REF_COLUMN_NAME'])
            $filter[$params['REF_COLUMN_NAME']] = $params['COLUMN_VALUE'];
        $listRS = $params['TABLE_NAME']::getList([
            'order' => ['ENTITY' => 'asc'],
            'filter' => $filter,
            'select' => ['*'],
        ])->fetchAll();

        $usersInfo = array_filter($listRS, fn($v) => mb_strpos($v['ENTITY'], 'U_') !== false);

        $userPreg = array_column($usersInfo, 'ENTITY');
        $userIds = array_map(function($v){
            return substr($v, 2, strlen($v));
        }, $userPreg);
        $userList = $this->getUsers(['ID' => $userIds]);

        $groupsList = $this->getGroupsList();


        if($this->arParams['PROJECT_GROUP_EXISTS']){
            $projectGroupsInfo = array_filter($listRS, fn($v) => mb_strpos($v['ENTITY'], 'PG_') !== false);
            $projectGroupsPreg = array_column($projectGroupsInfo, 'ENTITY');
            $projectGroupsIds = array_map(function($v){
                return substr($v, 2, strlen($v));
            }, $projectGroupsPreg);
            $projGroups = $this->getProjectGroups(['ID' => $projectGroupsIds]);
        }

        $otherAuthEntities = Entity\EditorManager::AUTH_USER_RULLS;
        $otherNotAuthEntities = Entity\EditorManager::NOT_AUTH_USER_RULLS;

        foreach ($listRS as $item){
            if($userList[$item['ENTITY']])
                $item['ENTITY_S'] = '<span class="entity-caption">Пользователь:</span> '.$userList[$item['ENTITY']];
            else if($projGroups[$item['ENTITY']])
                $item['ENTITY_S'] = '<span class="entity-caption">Группа проекта:</span> '.$projGroups[$item['ENTITY']];
            else if($otherAuthEntities[$item['ENTITY']])
                $item['ENTITY_S'] = '<span class="entity-caption">Другое:</span> '.$otherAuthEntities[$item['ENTITY']];
            else if($otherNotAuthEntities[$item['ENTITY']])
                $item['ENTITY_S'] = '<span class="entity-caption">Другое:</span> '.$otherNotAuthEntities[$item['ENTITY']];
            else if($groupsList[$item['ENTITY']])
                $item['ENTITY_S'] = '<span class="entity-caption">Группа:</span> '.$groupsList[$item['ENTITY']];

            $this->arResult['LIST'][] = $item;
        }

        return true;
    }

    function defineEntitySelectorItems(){
        $items = [];


        foreach (array_merge(Entity\EditorManager::NOT_AUTH_USER_RULLS, Entity\EditorManager::AUTH_USER_RULLS) as $id => $value ){
            $items[] = [
                'id' => $id,
                'entityId' => 'OTHER',
                'title' => $value,
                'tabs' => ['OTHER'],
            ];
        }


        if($this->arParams['PROJECT_GROUP_EXISTS']){

            foreach ($this->getProjectGroups() as $id => $value){
                $items[] =  [
                        'id' => $id,
                        'entityId' => 'PG',
                        'title' => $value,
                        'tabs' => ['PROJECT_GROUPS'],
                    ];
            }

        }

       foreach ($this->getUsers() as $id => $value) {
           $items[] = [
               'id' => $id,
               'entityId' => 'U',
               'title' => $value,
               'tabs' => ['USERS'],
           ];
       }

        foreach ($this->getGroupsList() as $id => $value) {
            $items[] = [
                'id' => $id,
                'entityId' => 'G',
                'title' => $value,
                'tabs' => ['GROUPS'],
            ];
        }



        $this->arParams['TAB_ITEMS'] = $items;
    }

    public function definePermission(){

        global $USER;

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($this->arParams['VARIABLES']['project_id']);

        if($this->arParams['VARIABLES']['project_id'] == 0)
            return true;

        if(array_intersect(['R', 'W', 'X'], $this->arParams['USER_PERMISSIONS']))
            return true;

        header('Location: '.Entity\UrlManager::getProjectLockedLink($this->arParams['VARIABLES']['project_id']));
    }

    public function executeComponent() {

        $this->definePermission();

        CJSCore::Init(array('rs.buttons', 'ui.notification', 'ui.buttons', "ui.forms", 'ui.list', 'ui.dialogs.messagebox'));

        if(!$this->defineList())
            return;

        $this->arParams['PROJECT_GROUP_EXISTS'] = Application::getConnection()->isTableExists($this->groupTable::getTableName()) && $this->arParams['PROJECT_GROUP_EXISTS'] == true;
        $this->arParams['PROJECT_GROUP_EXISTS_GROUP_ID'] = $this->arParams['VARIABLES']['project_id'];

        $this->defineEntitySelectorItems();

        $this->includeComponentTemplate();
    }
}
