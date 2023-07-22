<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Context,
    \Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper;



class ForumDiscussionList extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    public static function renameForumAction($id, $title){
        Orm\ForumTable::update($id, ['TITLE' => $title]);
    }

    public static function getComponentTemplateResultAction($params){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:forum.discussion.list",
            "",
            $params
        );

        return ob_get_clean();
    }

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineAdPermission($params['FORUM_ID']);


        return $params;
    }

    function defineContent(){
        if(!Loader::includeModule("gpi.workproject"))
            return;

        $themesTable = new Orm\ForumDiscussionTable();

        $viewPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['discussion.item'];
        $editPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['discussion.item.edit'];

        $this->arResult['THEMES'] = $themesTable::getList([
            'select' => ['AUTHOR_FIO', '*', 'LAST_NAME' => 'USER.LAST_NAME', 'NAME' => 'USER.NAME', 'SECOND_NAME' => 'USER.SECOND_NAME', 'LINK', 'EDIT_LINK', 'MESSAGE_IDS'],
            'filter' => [
                'FORUM_ID' => $this->arParams['FORUM_ID'],
            ],
            'runtime' => [
                'MESSAGES' => [
                    'data_type' => 'Gpi\Workproject\Orm\ForumDiscussionMessageTable',
                    'reference' => [
                        'this.ID' => 'ref.DISCUSSION_ID',
                    ]
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'MESSAGE_IDS',
                    'GROUP_CONCAT(%s)',
                    ['MESSAGES.ID']
                ),
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
                    ]
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'AUTHOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['USER.LAST_NAME','USER.NAME', 'USER.SECOND_NAME']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK',
                    'REPLACE("'.$viewPath.'", "#discussion_id#", %s)',
                    ['ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE("'.$editPath.'", "#discussion_id#", %s)',
                    ['ID']
                ),
            ]
        ])->fetchAll();
    }

    function defineParams(){
        $this->arResult['GRID_ID'] = 'workprojects_forum';
        $this->arResult['THEME_CREATE_LINK'] = $this->arParams['SEF_FOLDER'].str_replace('#discussion_id#', 0 ,$this->arParams['URL_TEMPLATES']['discussion.item.edit']);
        $this->arResult['SETTINGS_LINK'] = $this->arParams['SEF_FOLDER'].'settings/';

        global $APPLICATION;
        if($this->arParams['SET_BRANDCAMPS'] != 'N')
            $APPLICATION->AddChainItem('Форум', $this->arParams['SEF_FOLDER']);

        CJSCore::init(['ui.buttons', 'ui.buttons.icons', "sidepanel", 'bear.file.input', 'ui.list', 'ui.dialogs.messagebox', 'rs.buttons', 'cool.editor']);
    }

    function checkUpdates(){

        foreach ($this->arResult['THEMES'] as $theme){
            $messages = array_merge($messages, array_map(function($v) use ($theme){
                return [
                    'ID' => $v,
                    'DISCUSSION_ID' => $theme['ID'],
                    'FORUM_ID' => $theme['FORUM_ID'],
                ];
            }, explode(',',$theme['MESSAGE_IDS'])));
        }

        global $USER;
        $updatesRS = Orm\EntityUpdateTable::getList([
            'filter' => [
                'LOGIC' => 'OR',
                [
                    'USER.VALUE' => $USER->getId(),
                    'ENTITY_TYPE' => 'ForumDiscussion',
                    'ENTITY_ID' => array_column($this->arResult['THEMES'], 'ID'),
                ],
                [
                    'USER.VALUE' => $USER->getId(),
                    'ENTITY_TYPE' => 'ForumDiscussionMessage',
                    'ENTITY_ID' => explode(',', implode(',', array_filter(array_column($this->arResult['THEMES'], 'MESSAGE_IDS')))),
                ]
            ]
        ]);
        while($update = $updatesRS->fetch()){
            if($update['ENTITY_TYPE'] == 'ForumDiscussion'){
                $key =  array_search($update['ENTITY_ID'], array_column($this->arResult['THEMES'], 'ID'));
                $this->arResult['THEMES'][$key]['NEW_ID'] = $update['ID'];
            }else{
                $messageKey =  array_search($update['ENTITY_ID'], array_column($messages, 'ID'));
                $key =  array_search($messages[$messageKey]['DISCUSSION_ID'], array_column($this->arResult['THEMES'], 'ID'));
            }
            $this->arResult['THEMES'][$key]['IS_NEW'] = true;
        }
    }

    public function definePermission(){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineForumPermission($this->arParams['FORUM_ID']);

        if(array_intersect(['R', 'W', 'X'], $this->arParams['USER_PERMISSIONS']) )
            return true;

        header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
    }

    public function executeComponent() {

        $this->definePermission();

        $this->defineContent();
        $this->defineParams();
        $this->checkUpdates();

        $this->IncludeComponentTemplate();
    }

}
