<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Entity\UrlManager;


class RSDriveItems extends  \CBitrixComponent implements Controllerable{

    protected $folderSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 1024 1024" class="icon" version="1.1"><path d="M810.665 269.333v-24.889c0-27.467-22.288-49.777-49.777-49.777H437.333v-24.89c0-27.467-22.288-49.777-49.779-49.777H263.11c-27.489 0-49.777 22.31-49.777 49.777v99.556h597.332z" fill="#2577FF" style=" fill: #86d1d1; "></path><path d="M885.332 269.333H399.999V232c0-41.203-33.431-74.667-74.667-74.667H138.667C97.431 157.333 64 190.798 64 232v597.333C64 870.6 97.431 904 138.667 904h746.665c41.235 0 74.666-33.4 74.666-74.667V344c0-41.202-33.431-74.667-74.666-74.667z" fill="#FCB814" style=" fill: #ffd869; "></path><path d="M344 209.224v60.109h55.528c10.588-82.956-28.514-102.28-62.733-110.843A181.619 181.619 0 0 1 344 209.224zM896.794 270.491C901.296 280.096 904 290.7 904 302v485.334C904 828.6 870.566 862 829.332 862H82.665c-3.93 0-7.71-0.571-11.477-1.156C83.092 886.282 108.73 904 138.665 904h746.667C926.566 904 960 870.6 960 829.333V344c0-37.278-27.454-67.953-63.206-73.509z" fill="" style=" fill: #254574; "></path></svg>';

    public function configureActions(){
        return [
            'deleteNote' => [
                'prefilters' => [
                ]
            ]
        ];
    }

    public static function renameStorageAction($id, $title){
        Orm\StorageTable::update($id, ['TITLE' => $title]);
    }

    public static function createFolderAction($title, $storageId, $parentId = ''){
        global $USER;
        Loader::IncludeModule('gpi.workproject');
        $projectStorageObjectsTable = new Orm\StorageObjectTable();

        $result = $projectStorageObjectsTable::add([
            'TITLE' => $title,
            'TYPE' => 'FOLDER',
            'STORAGE_ID' => $storageId,
            'PARENT' => $parentId,
        ]);
        if (!$result->isSuccess()) {
            return json_encode([
                'status' => 1,
                'errors ' => $result->getErrorMessages()
            ]);
        } else {
            return json_encode([
                'status' => 1,
                'folderId' => $result->getId()
            ]);
        }


    }
    public static function loadFilesAction(){
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $data = $request->getPostList()->toArray();
        $files = $request->getFileList()->toArray();

        global $USER;
        Loader::IncludeModule('gpi.workproject');
        $projectStorageObjectsTable = new Orm\StorageObjectTable();


        foreach ($files as $fileHash){
            $result = $projectStorageObjectsTable::add([
                'TYPE' => 'FILE',
                'STORAGE_ID' => $data['STORAGE_ID'],
                'PARENT' => $data['PARENT_ID'],
                'CREATED_BY' => $USER->getId(),
                'FILE' => $fileHash,
            ]);
            if (!$result->isSuccess()) {
                return json_encode([
                    'status' => 0,
                    'errors ' => $result->getErrorMessages()
                ]);
            }
            $ids[] = $result->getId();

        }

        return json_encode([
            'status' => 1,
            'fileIds' => $ids
        ]);

    }
    public static function sendObjectListAction($update){
        Loader::IncludeModule('gpi.workproject');
        $projectStorageObjectsTable = new Orm\StorageObjectTable();

        foreach ($update as $data){
            $id = $data['ID'];

            unset($data['ID']);

            $result = $projectStorageObjectsTable::update($id, $data);

        }
    }
    public static function deleteObjectsAction($delete){

        Loader::IncludeModule('gpi.workproject');
        $projectStorageObjectsTable = new Orm\StorageObjectTable();

        foreach ($delete as $id) {
            $projectStorageObjectsTable::delete($id);
        }

        $findChilds = $delete;
        while($findChilds){
            $childs = $projectStorageObjectsTable::getList(['filter' => ['PARENT' => $findChilds]])->fetchAll();

            $findChilds = array_column($childs, 'ID');

            foreach ($findChilds as $id) {
                $projectStorageObjectsTable::delete($id);
            }
        }

        return json_encode([
            'status' => 1,
        ]);
    }
    public static function getComponentTemplateResultAction($params){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:storage",
            "",
            $params
        );

        return ob_get_clean();
    }

    function defineMap(){
        global $APPLICATION;
        $this->arResult['BRADCAMPS']=[];
        if(!$this->arParams['PARENT_ID'])
            return;

        $storageTable = new Orm\StorageObjectTable();
        $parent = $this->arParams['PARENT_ID'];
        $legend = [];
        while ($parent){


            $parentInfo = $storageTable::getById($parent)->fetch();
            $legend[] = [
                'title' => $parentInfo['TITLE'],
                'link' => $this->arParams['SEF_FOLDER'].$parentInfo['ID'].'/',
            ];
            $parent = $parentInfo['PARENT'];

        }

        foreach (array_reverse($legend) as $link){
            $APPLICATION->AddChainItem($link['title'], $link['link']);
        }

    }
    function defineColumns(){
        $this->arResult['COLUMNS'] = [
            'ID' => [
                'id' => 'ID',
                'name' => 'ID',
                'default' => true,
                'editable' => false,
                'type' => 'number',
                'sort' => 'ID',
            ],
            'TITLE' => [
                'id' => 'TITLE',
                'name' => 'Название',
                'default' => true,
                'editable' => true,
                'type' => 'text',
                'sort' => 'TITLE',
            ],
            'DESCRIPTION' => [
                'id' => 'DESCRIPTION',
                'name' => 'Описание',
                'default' => true,
                'editable' => true,
                'items' => $this->arResult['ACCESS_RIGHTS'],
                'type' => 'text',
                'sort' => 'DESCRIPTION',
            ],
            'CREATED_BY' => [
                'id' => 'CREATED_BY',
                'name' => 'Создано',
                'editable' => false,
                'sort' => 'CREATED_BY',
            ],
        ];
    }
    function defineActionBtns(){

        $snippet = new \Bitrix\Main\Grid\Panel\Snippet();

        $editBtn = $snippet->getEditButton();
        $editBtn['ONCHANGE'][0]['DATA'][0]['ONCHANGE'] = [
            [
                'ACTION' => 'CALLBACK',
                'DATA' => [
                    [
                        'JS' => 'runCoolGridDriveAction("save")',
                    ],
                ],
            ]
        ];

        $removeBtn = $snippet->getRemoveButton();
        $removeBtn['ONCHANGE'] = [
            [
                'ACTION' => 'CALLBACK',
                'CONFIRM' => 1,
                'CONFIRM_APPLY_BUTTON' => 'Удалить',
                'DATA' =>[
                    [
                        'JS' => 'runCoolGridDriveAction("delete")',
                    ],
                ],
                'CONFIRM_MESSAGE' => 'Подтвердите действие для отмеченных элементов',
                'CONFIRM_CANCEL_BUTTON' => 'Закрыть',
            ]
        ];

        $this->arResult['GRID_PARAMS']['ACTION_PANEL'] = [
            'GROUPS' => [
                [
                    'ITEMS' => [
                        $editBtn,
                        $removeBtn,
                    ],
                ],
            ]
        ];


    }
    function defineGridParams(){

        $grid_options = new Bitrix\Main\Grid\Options($this->arResult['GRID_PARAMS']['ID']);
        $nav_params = $grid_options->GetNavParams();

        $sort = ['TYPE' => 'desc'];

        if(current($grid_options->GetSorting()['sort']))
            $sort[current(array_keys($grid_options->GetSorting()['sort']))] = current($grid_options->GetSorting()['sort']);

        $this->arResult['GRID_PARAMS']['SORT'] = $sort;
        $this->arResult['GRID_PARAMS']['PAGE_SIZE'] = $nav_params['nPageSize'];
        $this->arResult['GRID_PARAMS']['CURRENT_PAGE'] = explode('-', $_REQUEST[$this->arResult['GRID_PARAMS']['ID']])[1]? explode('-', $_REQUEST[$this->arResult['GRID_PARAMS']['ID']])[1] : 1;
        $this->arResult['GRID_PARAMS']['SLICE_FROM']= ($this->arResult['GRID_PARAMS']['CURRENT_PAGE']-1) * $this->arResult['GRID_PARAMS']['PAGE_SIZE'];
        $this->arResult['GRID_PARAMS']['SLICE_TO']= $this->arResult['GRID_PARAMS']['CURRENT_PAGE'] * $this->arResult['GRID_PARAMS']['PAGE_SIZE'];
    }
    function defineFilter(){
        $filterOption = new \Bitrix\Main\UI\Filter\Options($this->arResult['GRID_PARAMS']['ID']);
        $filterData = $filterOption->getFilter([]);
        $filter = [];

        foreach ($filterData as $key => $value) {
            if(!$value || $value=='undefined')
                continue;

            if($key == 'FIND' || $key == 'USER'){
                $filter['FIO'] = "%$value%";
                continue;
            }

            if($this->arResult['COLUMNS'][$key]){
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

            if($this->arResult['COLUMNS'][$mirrorName]){
                $filter[$additProp.$mirrorName] = $value;
            }
        }

        $this->arResult['GRID_PARAMS']['FILTER'] = $filter;
    }
    function defineRows(){

        Loader::IncludeModule('gpi.workproject');

        global $USER;

        $userId = $USER->getId();

        $storageTable = new Orm\StorageObjectTable();

        $this->arResult['GRID_PARAMS']['FILTER']['STORAGE_ID'] = $this->arParams['STORAGE_ID'];
        $this->arResult['GRID_PARAMS']['FILTER']['PARENT'] = $this->arParams['PARENT_ID'];

        $this->arResult['GRID_PARAMS']['ITEMS_TOTAL'] = $storageTable::getList([
            'filter' => $this->arResult['GRID_PARAMS']['FILTER'],
            'runtime' => [
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
                    ]
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['USER.LAST_NAME','USER.NAME', 'USER.SECOND_NAME']
                ),
            ]
        ])->getSelectedRowsCount();

        $storageObjectsRS = $storageTable::getList([
            'select' => [
                '*',
                'FIO',
                'FILE_NAME' => 'FILE_INFO.ORIGINAL_NAME',
                'SUBDIR' => 'FILE_INFO.SUBDIR',
                'ORIGINAL_NAME' => 'FILE_INFO.ORIGINAL_NAME',
            ],
            'limit' => $this->arResult['GRID_PARAMS']['PAGE_SIZE'],
            'offset' => $this->arResult['GRID_PARAMS']['SLICE_FROM'],
            'filter' => $this->arResult['GRID_PARAMS']['FILTER'],
            'order' => $this->arResult['GRID_PARAMS']['SORT'],
            'runtime' => [
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
                    ]
                ],
                'FILE_INFO' => [
                    'data_type' => 'Bitrix\Main\FileTable',
                    'reference' => [
                        'this.FILE' => 'ref.ID',
                    ],
                    'join_type' => 'left',
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['USER.LAST_NAME','USER.NAME', 'USER.SECOND_NAME']
                ),
            ]
        ]);

        while($storage = $storageObjectsRS->fetch()){
            if($storage['FILE_NAME'] && !$storage['TITLE']){
                $storage['TITLE'] = $storage['FILE_NAME'];
            }

            $view = $storage;

            $fileLink = '/upload/'.$storage['SUBDIR'].'/'.$storage['FILE_NAME'];//

            $view['CREATED_BY'] = $storage['FIO'];

            $actions = [];

            if($storage['FILE_NAME']){
                $actions[] = [
                    'text'    => 'Открыть',
                    'onclick' => "showFile('".$fileLink."', '".$storage['TITLE']."');",
                    'default' => true,
                ];
                $storage['TITLE'] = '<a data-fancybox="gallery" href="'.$fileLink.'" data-caption="'.$storage['TITLE'].'" class="object"><div class="icon">'.$this->fileSvg.'</div>'.'<div class="title">'.$storage['TITLE'] ?? $storage['ORIGINAL_NAME'].'</div></a>';
            }else{
                $storage['TITLE'] = '<div class="object"><div class="icon">'.$this->folderSvg.'</div>'.'<div class="title">'.$storage['TITLE'].'</div></div>';

                $actions[] = [
                    'text'    => 'Открыть',
                    'onclick' => "window.SomeJsDriveClass.openFolder('".$this->arParams['SEF_FOLDER'].$storage['ID']."/');",
                    'default' => true,
                ];
            }

            if(array_intersect(['X'], $this->arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $this->arParams['USER_PERMISSIONS']) && $storage['CREATED_BY'] == $userId))
                $editable = 'true';
            else
                $editable = 'false';

            $this->arResult['ROWS'][] = [
                'data' => $view,
                'columns' => $storage,
                'actions' => $actions,
                'editable' => $editable
            ];

        }
    }

    function definePermission(){

        Loader::IncludeModule('gpi.workproject');

        $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineStoragePermission($this->arParams['STORAGE_ID']);

        if(array_intersect(['R', 'W', 'X'], $this->arParams['USER_PERMISSIONS']) )
            return true;

        header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
    }


    public function executeComponent() {

        $this->definePermission();

        global $APPLICATION;

        $APPLICATION->AddChainItem('Диск', $this->arParams['SEF_FOLDER']);

        if(!$this->arParams['STORAGE_ID']){
            showMessage('Нет ID диска');
            return;
        }

        $this->arResult['GRID_PARAMS']['ID'] = 'workproject_storage'.$this->arParams['STORAGE_ID'];
        $this->defineColumns();
        $this->defineActionBtns();
        $this->defineGridParams();
        $this->defineFilter();
        $this->defineRows();
        $this->defineMap();

        CJSCore::init(['jquery3.6.1', 'fancybox', 'bear.file.input', 'ui.notification', 'ui.entity-selector', 'ui.dialogs.messagebox', 'rs.buttons', 'ui.buttons', 'ui.buttons.icons', 'sidepanel']);
        $this->arParams['CURRENT_FOLDER'] = $this->arParams['SEF_FOLDER'];
        if($this->arParams['PARENT_ID'])
            $this->arParams['CURRENT_FOLDER'].=$this->arParams['PARENT_ID'].'/';

        $this->IncludeComponentTemplate();
    }

}
