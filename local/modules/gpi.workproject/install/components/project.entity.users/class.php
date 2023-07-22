<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Gpi\Workproject\Entity,
    Gpi\Workproject\Orm,
    Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable;


class ProjectEntityUsers extends  \CBitrixComponent implements Controllerable{
    protected $projectTable =   'Gpi\Workproject\Orm\ProjectTable';
    protected $groupItemTable = 'Gpi\Workproject\Orm\GroupItemTable';
    protected $directionTable = 'Gpi\Workproject\Orm\ProjectDirectionTable';
    protected $usersTable =     'Gpi\Workproject\Orm\ProjectUserTable';

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if($params['VARIABLES']['group_id'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineGroupPermission($params['VARIABLES']['project_id'], $params['VARIABLES']['group_id']);
        else
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($params['VARIABLES']['project_id']);

        return $params;
    }

    public function configureActions(){
        return [
            'deleteNote' => [
                'prefilters' => [
                ]
            ]
        ];
    }

    public static function sendUsersListAction($add = [], $update = [], $projectId){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        foreach ($add as $addData){

            $addData['GROUPS'] = array_column(json_decode($addData['GROUPS']), 'VALUE');
            $addData['CATEGORY'] = array_column(json_decode($addData['CATEGORY']), 'VALUE');
            $addData = array_merge($addData, [
                'PROJECT_ID' => $projectId,
            ]);

            Orm\ProjectUserTable::add($addData);
        }

        foreach ($update as $updateData){
            $id = $updateData['ID'];
            unset($updateData['ID']);
            $updateData['GROUPS'] = array_column(json_decode($updateData['GROUPS']), 'VALUE');
            $updateData['CATEGORY'] = array_column(json_decode($updateData['CATEGORY']), 'VALUE');
            $updateData = array_merge($updateData, [
                'PROJECT_ID' => $projectId,
            ]);

            Orm\ProjectUserTable::update($id, $updateData);
        }
    }
    public static function deleteUsersAction($delete){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        foreach ($delete as $deleteAr){
            Orm\ProjectUserTable::delete($deleteAr['ID']);
        }

    }


    function defineColumns(){
        $this->arResult['COLUMNS'] = [
            'ID' => [
                'id' => 'ID',
                'name' => 'ID',
                'editable' => false,
                'type' => 'number',
                'sort' => 'ID',
            ],
            'USER_ID' => [
                'id' => 'USER_ID',
                'name' => 'Работник',
                'default' => true,
                'editable' => true,
                'type' => 'text',
                'sort' => 'USER_ID',
            ],
            'GROUPS' => [
                'id' => 'GROUPS',
                'name' => 'Группы',
                'required' => true,
                'default' => true,
                'editable' => [
                    'items' => $this->arResult['GROUPS'],
                    'multiple' => true,
                ],
                'items' => $this->arResult['GROUPS'],
                'type' => 'multiselect',
                'sort' => 'GROUPS',
            ],
            'CATEGORY' => [
                'id' => 'CATEGORY',
                'name' => 'Роль',
                'required' => true,
                'default' => 1,
                'editable' => [
                    'items' => $this->arResult['CATEGORIES'],
                    'multiple' => true,
                ],
                'items' => $this->arResult['CATEGORIES'],
                'type' => 'multiselect',
                'sort' => 'GROUPS',
            ],
        ];
    }
    function defineActionBtns(){


        if(array_intersect(['X'], $this->arParams['USER_PERMISSIONS'])){
            $snippet = new \Bitrix\Main\Grid\Panel\Snippet();

            $editBtn = $snippet->getEditButton();
            $editBtn['ONCHANGE'][0]['DATA'][0]['ONCHANGE'] = [
                [
                    'ACTION' => 'CALLBACK',
                    'DATA' => [
                        [
                            'JS' => 'runCoolGridAction("save")',
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
                            'JS' => 'runCoolGridAction("delete")',
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
                            [
                                'TYPE' => 'BUTTON',
                                'ID' => 'grid_add_button',
                                'NAME' => NULL,
                                'HREF' => 'javascript:void(0);',
                                'CLASS' => 'main-grid-buttons save',
                                'TEXT' => 'Добавить',
                                'ONCHANGE' =>
                                    [
                                        [
                                            'ACTION' => 'CALLBACK',
                                            'DATA' => [
                                                [
                                                    'JS' => 'runCoolGridAction("addRow")',
                                                ],
                                            ],
                                        ],
                                        [
                                            'ACTION' => 'CREATE',
                                            'DATA' => [
                                                [
                                                    'TYPE' => 'BUTTON',
                                                    'ID' => 'grid_save_button',
                                                    'NAME' => 'BUTTON',
                                                    'CLASS' => 'save',
                                                    'TEXT' => 'Сохранить',
                                                    'ONCHANGE' => [
                                                        [
                                                            'ACTION' => 'CALLBACK',
                                                            'DATA' => [
                                                                [
                                                                    'JS' => 'runCoolGridAction("save")',
                                                                ],
                                                            ],
                                                        ],
                                                        [
                                                            'ACTION' => 'CALLBACK',
                                                            'DATA' => [
                                                                [
                                                                    'JS' => 'runCoolGridAction("hidePanel")',
                                                                ],
                                                            ],
                                                        ]
                                                    ]
                                                ],
                                                [
                                                    'TYPE' => 'BUTTON',
                                                    'ID' => 'grid_cancel_button',
                                                    'NAME' => 'BUTTON',
                                                    'CLASS' => 'cancel',
                                                    'TEXT' => 'Отменить',
                                                    'ONCHANGE' => [
                                                        [
                                                            'ACTION' => 'CALLBACK',
                                                            'DATA' => [
                                                                [
                                                                    'JS' => 'runCoolGridAction("removeTemplateItems")',
                                                                ],
                                                            ],
                                                        ],
                                                        [
                                                            'ACTION' => 'SHOW_ALL',
                                                            'DATA' => []
                                                        ],
                                                        [
                                                            'ACTION' => 'REMOVE',
                                                            'DATA' => [
                                                                [
                                                                    'ID' => 'grid_save_button',
                                                                ],
                                                                [
                                                                    'ID' => 'grid_cancel_button',
                                                                ],
                                                                [
                                                                    'ID' => 'grid_cancel_button',
                                                                ],
                                                            ]
                                                        ],
                                                        [
                                                            'ACTION' => 'CALLBACK',
                                                            'DATA' => [
                                                                [
                                                                    'JS' => 'runCoolGridAction("checkCorrectOanelShow")',
                                                                ],
                                                            ],
                                                        ]
                                                    ]
                                                ],
                                            ]
                                        ],
                                        [
                                            'ACTION' => 'HIDE_ALL_EXPECT',
                                            'DATA' => [
                                                [
                                                    'ID' => 'grid_save_button',
                                                ],
                                                [
                                                    'ID' => 'grid_cancel_button',
                                                ],
                                                [
                                                    'ID' => 'grid_cancel_button',
                                                ],
                                            ]
                                        ],
                                        [
                                            'ACTION' => 'CALLBACK',
                                            'DATA' => [
                                                [
                                                    'JS' => 'runCoolGridAction("showPanel")',
                                                ],
                                            ],
                                        ]
                                    ],
                            ],
                            $editBtn,
                            $removeBtn,
                            $snippet->getForAllCheckbox(),
                        ],
                    ],
                ]
            ];
        }
    }
    function defineGridParams(){

        $grid_options = new Bitrix\Main\Grid\Options($this->arResult['GRID_PARAMS']['ID']);
        $nav_params = $grid_options->GetNavParams();

        $this->arResult['GRID_PARAMS']['SORT'] = $grid_options->GetSorting()['sort'];
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

            if($key == 'FIND' || $key == 'USER_ID'){
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

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public static function getEmployyerInputAction($value, $multiple, $code){
        $data = [
            'MULTIPLE' => $multiple,
            'CODE' => $code,
            'VALUE' => $value,
            'FIELD_ID' => 'PROPERTY_'.self::generateRandomString(),
            'PROPERTY_TYPE' => 'S',
            'TYPE' => 'S:employee',
            'PROPERTY_USER_TYPE' =>
                array (
                    'PROPERTY_TYPE' => 'S',
                    'USER_TYPE' => 'employee',
                    'DESCRIPTION' => 'Привязка к сотруднику',
                    'GetPropertyFieldHtml' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetPropertyFieldHtml',
                        ),
                    'GetAdminListViewHTML' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetAdminListViewHTML',
                        ),
                    'GetPublicViewHTML' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetPublicViewHTML',
                        ),
                    'GetPublicEditHTML' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetPublicEditHTML',
                        ),
                    'GetPublicEditHTMLMulty' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetPublicEditHTMLMulty',
                        ),
                    'GetPublicFilterHTML' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetPublicFilterHTML',
                        ),
                    'GetUIFilterProperty' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetUIFilterProperty',
                        ),
                    'ConvertToDB' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'ConvertFromToDB',
                        ),
                    'CheckFields' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'CheckFields',
                        ),
                    'GetLength' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetLength',
                        ),
                    'GetUIEntityEditorProperty' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetUIEntityEditorProperty',
                        ),
                    'GetUIEntityEditorPropertyEditHtml' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetUIEntityEditorPropertyEditHtml',
                        ),
                    'GetUIEntityEditorPropertyViewHtml' =>
                        array (
                            0 => 'CIBlockPropertyEmployee',
                            1 => 'GetUIEntityEditorPropertyViewHtml',
                        ),
                ),
        ];

        Bitrix\Main\Loader::IncludeModule('lists');

        return \Bitrix\Lists\Field::prepareFieldDataForEditForm($data)['value'];
    }


    protected function defineManagers(){
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
            const managersList = ".json_encode($users).";
        </script>
        ");
    }


    function defineRows(){

        $acts = $this->arResult['DIRECTIONS'];
        $groups = $this->arResult['GROUPS'];
        $categories = $this->arResult['CATEGORIES_DESCS'];

        $this->arResult['GRID_PARAMS']['FILTER']['PROJECT_ID'] = $this->arParams['VARIABLES']['project_id'];

        if($this->arParams['VARIABLES']['group_id'] && !$this->arResult['GRID_PARAMS']['FILTER']['GROUPS'])
            $this->arResult['GRID_PARAMS']['FILTER']['GROUP.VALUE'] = $this->arParams['VARIABLES']['group_id'];

        $this->arResult['GRID_PARAMS']['ITEMS_TOTAL'] = $this->usersTable::getList([
            'filter' => $this->arResult['GRID_PARAMS']['FILTER'],
            'runtime' => [
                'GROUP' => [
                    'data_type' => 'Gpi\Workproject\Orm\ProjectUserGroupsTable',
                    'reference' => [
                        'this.ID' => 'ref.VALUE_ID',
                    ]
                ],
            ]
        ])->getSelectedRowsCount();
        $usersListRS = $this->usersTable::getList([
            'select' => [
                'ID',
                'USER_ID',
                'FIO',
                'GROUPS',
                'CATEGORY'
            ],
            'limit' => $this->arResult['GRID_PARAMS']['PAGE_SIZE'],
            'offset' => $this->arResult['GRID_PARAMS']['SLICE_FROM'],
            'filter' => $this->arResult['GRID_PARAMS']['FILTER'],
            'runtime' => [
                'GROUP' => [
                    'data_type' => 'Gpi\Workproject\Orm\ProjectUserGroupsTable',
                    'reference' => [
                        'this.ID' => 'ref.VALUE_ID',
                    ]
                ],
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
                    'FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['AUTHOR_LAST_NAME','AUTHOR_NAME', 'AUTHOR_SECOND_NAME']
                ),
            ]
        ]);
        while($user = $usersListRS->fetch()){
            $view = $user;
            $view['USER_ID'] = $user['FIO'];

            $view['DIRECTIONS'] = implode(', ',array_map(function ($v) use($acts) {return $acts[$v];}, $view['DIRECTIONS']));
            $view['GROUPS'] = implode(', ',array_map(function ($v) use($groups) { return $groups[$v];}, $view['GROUPS']));
            $view['CATEGORY'] = implode(', ',array_map(function ($v) use($categories) { return $categories[$v];}, $view['CATEGORY']));

            $this->arResult['ROWS'][] = [
                'data' => $user,
                'columns' => $view,
            ];

        }

    }
    function defineGroups(){
        $filter = ['UNION_ID' => $this->arParams['PROJECT']['GROUP_UNION_ID']];
        if($this->arParams['VARIABLES']['group_id'])
            $filter['ID'] = $this->arParams['VARIABLES']['group_id'];

        $groupsRS = $this->groupItemTable::getList([
            'select' => ['ID', 'TITLE', 'LINKED_DIRECTION_TITLE' => 'LINKED_DIRECTION.TITLE'],
            'filter' => $filter,
            'order' => [
                'LINKED_DIRECTION_TITLE' => 'asc',
                'TITLE' => 'asc',
                ],
            'runtime' => [
                'LINKED_DIRECTION' => [
                    'data_type' => $this->directionTable,
                    'reference' => [
                        'this.DIRECTION' => 'ref.ID',
                    ]
                ]
            ]
        ]);

        while($group = $groupsRS->fetch()){
            $this->arResult['GROUPS'][$group["ID"]] = "{$group['TITLE']} ({$group['LINKED_DIRECTION_TITLE']})";
        }

    }
    function defineDirections(){
        $directionsRS = $this->directionTable::getList([
            'filter' => ['PROJECT_ID' => $this->arParams['VARIABLES']['project_id']]
        ]);

        while($direction = $directionsRS->fetch()){
            $this->arResult['DIRECTIONS'][$direction["ID"]] = $direction["TITLE"];
        }
    }
    function defineProject(){
        if(!$this->arParams['PROJECT'])
            $this->arParams['PROJECT'] = $this->projectTable::getById($this->arParams['VARIABLES']['project_id'])->fetch();
    }

    function defineCategories(){
        $this->arResult['CATEGORIES'] = Orm\ProjectUserTable::CATEGORY;
        $this->arResult['CATEGORIES_DESCS'] = Orm\ProjectUserTable::CATEGORY_SHORT;
        $this->arResult['CATEGORIES_EDITS'] = Orm\ProjectUserTable::EDITED_CATEGORIES;
        $this->arResult['CATEGORIES_CONSTS'] = array_diff(array_keys(Orm\ProjectUserTable::CATEGORY), Orm\ProjectUserTable::EDITED_CATEGORIES);
    }

    public function definePermission(){

        global $USER;

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'] && $this->arParams['VARIABLES']['group_id'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineGroupPermission($this->arParams['VARIABLES']['group_id']);
        else if(!$this->arParams['USER_PERMISSIONS'] && $this->arParams['VARIABLES']['project_id'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineProjectPermission($this->arParams['VARIABLES']['project_id']);

        if(array_intersect(['R', 'W', 'X'], $this->arParams['USER_PERMISSIONS']))
            return true;

        if($this->arParams['VARIABLES']['group_id'])
            header('Location: '.Entity\UrlManager::getGroupLockedLink($this->arParams['VARIABLES']['project_id'], $this->arParams['VARIABLES']['group_id']));
        else
            header('Location: '.Entity\UrlManager::getProjectLockedLink($this->arParams['VARIABLES']['project_id']));
    }


    public function executeComponent() {

        CJSCore::Init(['sidepanel', 'lists', 'iblock', 'ui.entity-selector', 'ui.notification']);
        $this->arResult['GRID_PARAMS']['ID'] = 'workproject_users_list';

        $this->definePermission();
        $this->definePermission();

        $this->defineManagers();
        $this->defineProject();
        $this->defineDirections();
        $this->defineGroups();
        $this->defineCategories();

        $this->defineColumns();
        $this->defineActionBtns();
        $this->defineGridParams();
        $this->defineFilter();
        $this->defineRows();

        global $APPLICATION;

        if($this->arParams['VARIABLES']['group_id'])
            $APPLICATION->AddChainItem('Список участников', Entity\UrlManager::getGroupUserListLink($this->arParams['VARIABLES']['project_id'], $this->arParams['VARIABLES']['group_id']));
        else
            $APPLICATION->AddChainItem('Список участников', Entity\UrlManager::getProjectUserListLink($this->arParams['VARIABLES']['project_id']));

        $this->IncludeComponentTemplate();
    }

}
