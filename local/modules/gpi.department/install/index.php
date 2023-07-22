<?
declare(strict_types = 1);
use Bitrix\Main\Application,
    Bitrix\Main\Loader,
    CUserTypeEntity,
    Bitrix\Main\Config\Option,
    Gpi\Workproject\Orm;

class gpi_department extends CModule
{

    protected $arrivalObjs=[
        [
            'from' => "/local/modules/#module_id#/install/admin/#module_id#.php",
            'to' => "/bitrix/admin/#module_id#.php",
        ],
        [
            'from' => "/local/modules/#module_id#/install/js/",
            'to' => "/bitrix/js/#module_id#/",
            'is_path' => true,
        ],
    ];

    protected $eventsHandls=[
        [
            'module' => 'main',
            'event' => 'OnBeforeProlog',
            'class' => 'Gpi\Workproject\Events\OnBeforeProlog',
            'method' => 'Listen',
        ],
    ];

    protected $agents=[
        [
            'classMethod' => '',
            'isPeriod' => '',
            'interval' => '',
            'active' => '',
        ],
    ];

    protected $tables=[
        'Gpi\Workproject\Orm\DepartmentDirectoriesTable',
        'Gpi\Workproject\Orm\DepartmentUserTable',
        'Gpi\Workproject\Orm\DepartmentTable',
        'Gpi\Workproject\Orm\ComponentTemplateTable',
        'Gpi\Workproject\Orm\AdUnionUserPermissionTable',
        'Gpi\Workproject\Orm\AdItemTable',
        'Gpi\Workproject\Orm\AdUnionTable',
        'Gpi\Workproject\Orm\CalendarEventTable',
        'Gpi\Workproject\Orm\CalendarUserPermissionTable',
        'Gpi\Workproject\Orm\CalendarTable',
        'Gpi\Workproject\Orm\EntityUpdateTable',
        'Gpi\Workproject\Orm\EntityUpdateUserTable',
        'Gpi\Workproject\Orm\EntityCommentsTable',
        'Gpi\Workproject\Orm\ForumDiscussionMessageTable',
        'Gpi\Workproject\Orm\ForumDiscussionTable',
        'Gpi\Workproject\Orm\ForumUserPermissionTable',
        'Gpi\Workproject\Orm\ForumTable',
        'Gpi\Workproject\Orm\GroupItemUserPermissionTable',
        'Gpi\Workproject\Orm\GroupItemTable',
        'Gpi\Workproject\Orm\GroupUnionTable',
        'Gpi\Workproject\Orm\ProjectDirectionTable',
        'Gpi\Workproject\Orm\ProjectUserPermissionTable',
        'Gpi\Workproject\Orm\ProjectUserTable',
        'Gpi\Workproject\Orm\ProjectUserGroupsTable',
        'Gpi\Workproject\Orm\ProjectUserCategoryTable',
        'Gpi\Workproject\Orm\ProjectTable',
        'Gpi\Workproject\Orm\StorageUserPermissionTable',
        'Gpi\Workproject\Orm\StorageObjectTable',
        'Gpi\Workproject\Orm\StorageTable',
        'Gpi\Workproject\Orm\TasksItemTable',
        'Gpi\Workproject\Orm\TasksItemApprovalTable',
        'Gpi\Workproject\Orm\GalleryPermissionTable',
        'Gpi\Workproject\Orm\GalleryAlbumItemTable',
        'Gpi\Workproject\Orm\GalleryAlbumTable',
        'Gpi\Workproject\Orm\GalleryTable',
        'Gpi\Workproject\Orm\ActivityDirectionPermissionTable',
        'Gpi\Workproject\Orm\ActivityDirectionTable',
        'Gpi\Workproject\Orm\DetailDirectionEventTable',
        'Gpi\Workproject\Orm\DetailDirectionDocumentTable',
        'Gpi\Workproject\Orm\DetailDirectionImportantTable',
        'Gpi\Workproject\Orm\DetailDirectionOrderTable',
        'Gpi\Workproject\Orm\DetailDirectionTable',
        'Gpi\Workproject\Orm\NotesTable',
    ];

    protected $userFields=[
        [
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_SHOW_COMPACT_NOTES_RS',
            'USER_TYPE_ID' => 'boolean',
            'MULTIPLE' => 'N',
            'SHOW_FILTER' => 'Y',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'SETTINGS' => [
                'DEFAULT_VALUE' => 1
            ]
        ],
        [
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_THEME_RS',
            'USER_TYPE_ID' => 'string',
            'MULTIPLE' => 'N',
            'SHOW_FILTER' => 'Y',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'SETTINGS' => [
                'DEFAULT_VALUE' => 'WHITE'
            ]
        ],
        [
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_COMPACT_BLOCK_CONTENT',
            'USER_TYPE_ID' => 'string',
            'MULTIPLE' => 'N',
            'SHOW_FILTER' => 'Y',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'SETTINGS' => [
                'DEFAULT_VALUE' => 'NOTES'
            ]
        ],
        [
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => 'UF_FAVORIT_INDUSTRIAL_SERVICES',
            'USER_TYPE_ID' => 'string',
            'MULTIPLE' => 'N',
            'SHOW_FILTER' => 'Y',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
        ]
    ];

    public function __construct()
    {
        $arModuleVersion = array();
        include_once(__DIR__ . '/version.php');
        $this->MODULE_ID = str_replace("_", ".", get_class($this));
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = GetMessage("MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("MODULE_DESCRIPTION");
        $this->PARTNER_NAME = GetMessage("PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("PARTNER_URI");
        $this->isDev = true;
        $this->setUpdatesHadle();
    }

    function DoInstall()
    {

        global $DOCUMENT_ROOT, $APPLICATION;
        if (!$this->installFiles()) {
            return false;
        }
        RegisterModule($this->MODULE_ID);
        Loader::includeModule($this->MODULE_ID);

        $this->installDB();
        $this->registerAgents();
        $this->registerListeners();
        $this->createUserFields();
    }
    function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION, $step;

        switch ($step){

            case 1:
                $this->unRegisterListeners();
                $this->unRegisterAgents();
                if($_REQUEST['save_tables']!='Y'){
                    $this->uninstallDB();
                }
                $this->UnInstallFiles();
                $this->deleteUserFields();
                UnRegisterModule($this->MODULE_ID);
                break;

            default:
                $APPLICATION->IncludeAdminFile(GetMessage("MODULE_UNINSTALL") . $this->MODULE_NAME, __DIR__."/unstep.php");
                break;

        }

    }


    function installFiles()
    {

        foreach ($this->arrivalObjs as $arrivalObj){
            $arrivalObj['from'] = str_replace('#module_id#', $this->MODULE_ID, $arrivalObj['from']);
            $arrivalObj['to'] = str_replace('#module_id#', $this->MODULE_ID, $arrivalObj['to']);

            if($arrivalObj['is_path'])
                CheckDirPath($arrivalObj['to']);

            CopyDirFiles($_SERVER['DOCUMENT_ROOT']. $arrivalObj['from'], $_SERVER['DOCUMENT_ROOT']. $arrivalObj['to'], true, true);
        }

        return true;
    }
    function unInstallFiles()
    {

        if($this->isDev){
            foreach ($this->arrivalObjs as $arrivalObj){
                $arrivalObj['from'] = str_replace('#module_id#', $this->MODULE_ID, $arrivalObj['from']);
                $arrivalObj['to'] = str_replace('#module_id#', $this->MODULE_ID, $arrivalObj['to']);
                DeleteDirFilesEx($arrivalObj['from']);
                if($arrivalObj['is_path'])
                    CheckDirPath($arrivalObj['from']);

                CopyDirFiles($_SERVER['DOCUMENT_ROOT']. $arrivalObj['to'], $_SERVER['DOCUMENT_ROOT']. $arrivalObj['from'], true, true);
            }
        }

        foreach ($this->arrivalObjs as $arrivalObj){
            $arrivalObj['to'] = str_replace('#module_id#', $this->MODULE_ID, $arrivalObj['to']);

            DeleteDirFilesEx($arrivalObj['to']);
        }

        return true;
    }


    function registerListeners(){

        foreach (array_filter($this->eventsHandls, fn ($v) => $v['event'] != '') as $handle){
            RegisterModuleDependences(
                $handle['module'],
                $handle['event'],
                $this->MODULE_ID,
                $handle['class'],
                $handle['method']
            );
        }
    }
    function unRegisterListeners(){

        foreach (array_filter($this->eventsHandls, fn ($v) => $v['event'] != '') as $handle){
            UnRegisterModuleDependences(
                $handle['module'],
                $handle['event'],
                $this->MODULE_ID,
                $handle['class'],
                $handle['method']
            );
        }

    }

    function registerAgents(){

        foreach (array_filter($this->agents, fn ($v) => $v['classMethod'] != '') as $agent){
            \CAgent::AddAgent($agent['classMethod'], $this->MODULE_ID, $agent['isPeriod'], $agent['interval'], "", $agent['active']);
        }

    }
    function unRegisterAgents(){

        foreach (array_filter($this->agents, fn ($v) => $v['classMethod'] != '') as $agent){
            \CAgent::RemoveAgent($agent['classMethod'], $this->MODULE_ID);
        }

    }


    function installDB()
    {
        Loader::includeModule($this->MODULE_ID);
        $connector = Application::getConnection();

        foreach ($this->tables as $tableName){
            if (!$connector->isTableExists($tableName::getTableName())){
                $tableName::getEntity()->createDbTable();
                $this->onAfterTablesCreate($tableName);
            }
        }
        return true;
    }
    function uninstallDB()
    {
        Loader::includeModule($this->MODULE_ID);
        $connector = Application::getConnection();

        foreach ($this->tables as $tableName){
            if ($connector->isTableExists($tableName::getTableName())){
                $tableConnection = $tableName::getEntity()->getConnection();
                $tableConnection->dropTable($tableName::getTableName());
            }
        }

        return true;
    }
    function onAfterTablesCreate($tableName){

        global $USER;
        $userId = $USER->getId();
        switch ($tableName){
            case 'Gpi\Workproject\Orm\CalendarTable':
                Gpi\Workproject\Orm\CalendarTable::add(['TITLE' => 'Общий календарь']);
                break;

            case 'Gpi\Workproject\Orm\AdUnionTable':
                Gpi\Workproject\Orm\AdUnionTable::add(['TITLE' => 'Общиe события']);
                break;

            case 'Gpi\Workproject\Orm\ForumTable':
                Gpi\Workproject\Orm\ForumTable::add(['TITLE' => 'Общий форум']);
                break;

            case 'Gpi\Workproject\Orm\StorageTable':
                Gpi\Workproject\Orm\StorageTable::add(['TITLE' => 'Общий диск']);
                break;

            case 'Gpi\Workproject\Orm\GalleryTable':
                Gpi\Workproject\Orm\GalleryTable::add(['TITLE' => 'Общая галлерея']);
                break;

            case 'ActivityDirectionTable':
                ActivityDirectionPermissionTable::add([
                    'PERMISSION' => 'X',
                    'ENTITY' => "U_$userId",
                ]);
                break;
        }
    }

    function createUserFields(){
        $obUserField = new CUserTypeEntity;

        foreach ($this->userFields as $fieldSet){
            $fieldId = $obUserField->add($fieldSet);
            if($fieldId)
                Option::set($this->MODULE_ID, "{$fieldSet['FIELD_NAME']}_ID", $fieldId);
        }
    }
    function deleteUserFields(){
        $obUserField = new CUserTypeEntity;

        foreach ($this->userFields as $fieldSet){
            $fieldId = Option::get($this->MODULE_ID, "{$fieldSet['FIELD_NAME']}_ID");
            if($fieldId)
                $obUserField->delete($fieldId);
        }
    }


    function setUpdatesHadle(){

        $tables = [
            'CalendarEvent',
            'DetailDirectionDocument',
            'DetailDirectionOrder',
            'DetailDirectionEvent',
            'DetailDirectionImportant',
            'ForumDiscussionMessage',
            'ForumDiscussion',
            'AdItem',
            'GroupItem',
            'ProjectDirection',
            'ProjectTaskItems',
            'StorageObject',
            'GalleryAlbumItem',
            'ProjectDirection',
            'CalendarEvent',
            'TasksItem',
            'StorageObject',
            'GroupItem',
        ];

        foreach($tables as $table){
            $this->eventsHandls[] =[
                'module' => $this->MODULE_ID,
                'event' => "{$table}OnAfterAdd",
                'class' => 'Gpi\Workproject\Entity\Updates\Writer',
                'method' => 'addNewRecord',
            ];
            $this->eventsHandls[] =[
                'module' => $this->MODULE_ID,
                'event' => "{$table}OnAfterUpdate",
                'class' => 'Gpi\Workproject\Entity\Updates\Writer',
                'method' => 'addNewRecord',
            ];
            $this->eventsHandls[] =[
                'module' => $this->MODULE_ID,
                'event' => "{$table}OnAfterDelete",
                'class' => 'Gpi\Workproject\Entity\Updates\Writer',
                'method' => 'deleteRecord',
            ];
        }

    }

}
