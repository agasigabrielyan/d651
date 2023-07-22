<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Context,
    \Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper;



class ForumDiscussionListItem extends  \CBitrixComponent implements Controllerable{


    public function configureActions(){}

    public static function getComponentTemplateResultAction($params){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:forum.discussion.list.item",
            "",
            $params
        );

        return ob_get_clean();
    }

    function defineContent(){
        if(!Loader::includeModule("gpi.workproject"))
            return;

        $messagesTable = new Orm\ForumDiscussionMessageTable();

        $messageFiles=[];
        $answerPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['discussion.message.answer'];
        $editPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['discussion.message.edit'];

        $messagesRS = $messagesTable::getList([
            'filter' => [
                'DISCUSSION_ID' => $this->arParams['DISCUSSION_ID'],
            ],
            'order' => ['ID' => 'asc'],
            'select' => ['AUTHOR_FIO', '*', 'LAST_NAME' => 'USER.LAST_NAME', 'NAME' => 'USER.NAME', 'SECOND_NAME' => 'USER.SECOND_NAME', 'ANSWER_LINK', 'EDIT_LINK'],
            'runtime' => [
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
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
                    'AUTHOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['AUTHOR_LAST_NAME','AUTHOR_NAME', 'AUTHOR_SECOND_NAME']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'ANSWER_LINK_RS',
                    'REPLACE("'.$answerPath.'", "#discussion_id#", %s)',
                    ['DISCUSSION_ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK_RS',
                    'REPLACE("'.$editPath.'", "#discussion_id#", %s)',
                    ['DISCUSSION_ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'ANSWER_LINK',
                    'REPLACE(%s, "#message_id#", %s)',
                    ['ANSWER_LINK_RS', 'ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE(%s, "#message_id#", %s)',
                    ['EDIT_LINK_RS', 'ID']
                ),
            ]
        ])->fetchAll();

        $filesRS= array_column($messagesRS, 'FILES');
        $fileList = [];
        foreach ($filesRS as $fileListRS){
            $fileList = array_merge($fileList, $fileListRS);
        }

        $messageFilesRS = Bitrix\Main\FileTable::getList([
            'select' => ['PATH', '*'],
            'filter' => ['ID' => $fileList],
            'runtime' => [
                new Bitrix\Main\Entity\ExpressionField(
                    'PATH',
                    'CONCAT("/upload/", %s, "/", %s)',
                    ['SUBDIR', 'FILE_NAME']
                )
            ]
        ]);

        while($file = $messageFilesRS->fetch()){
            $messageFiles[$file['ID']] = $file;
        }


        foreach ($messagesRS as $message) {

            $message['FILES'] = array_map(function($v) use ($messageFiles) {
                return $messageFiles[$v];
            }, $message['FILES']);

            $this->arResult['MESSAGE_USERS'][$message['ID']] = [
                'AUTHOR_FIO' => $message['AUTHOR_FIO'],
                'AUTHOR' => $message['AUTHOR'],
                'LAST_NAME' => $message['LAST_NAME'],
                'NAME' => $message['NAME'],
                'SECOND_NAME' => $message['SECOND_NAME'],
            ];
            if (!$message['PARENT_ID'])
                $this->arResult['MESSAGES'][$message['ID']] = $message;
            else {
                $qradation[$message['ID']] = $message['PARENT_ID'];
                $key = $qradation[$message['ID']];
                $count =1;
                while ($qradation[$key] && $count<50) {
                    $key = $qradation[$key];
                    $count++;
                }
                $this->arResult['MESSAGES'][$key]['COMMENTS'][] = $message;
            }
        }

        $themesTable = new Orm\ForumDiscussionTable();
        $viewPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['discussion.item'];
        $editPath = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['discussion.item.edit'];
        $this->arResult['THEME'] = $themesTable::getList([
            'select' => [
                'AUTHOR_FIO', '*', 'USER.LAST_NAME', 'EDIT_LINK', 'LINK'
            ],
            'filter' => [
                'ID' => $this->arParams['DISCUSSION_ID'],
            ],
            'runtime' => [
                'USER' => [
                    'data_type' => '\Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
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
                    'AUTHOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['AUTHOR_LAST_NAME','AUTHOR_NAME', 'AUTHOR_SECOND_NAME']
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
        ])->fetch();
        

        if(!$this->arResult['THEME']['FILES'])
            return;


        $this->arResult['THEME']['FILES'] = Bitrix\Main\FileTable::getList([
            'select' => ['PATH', '*'],
            'filter' => ['ID' => $this->arResult['THEME']['FILES']],
            'runtime' => [
                new Bitrix\Main\Entity\ExpressionField(
                    'PATH',
                    'CONCAT("/upload/", %s, "/", %s)',
                    ['SUBDIR', 'FILE_NAME']
                )
            ]
        ])->fetchAll();

    }

    function definePermission(){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineForumPermission($this->arParams['FORUM_ID']);

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

    function defineParams(){
        global $APPLICATION;


        $context = Bitrix\Main\Application::getInstance()->getContext();
        $server = $context->getServer();

        $APPLICATION->AddChainItem('Форум', $this->arParams['SEF_FOLDER']);
        $APPLICATION->AddChainItem('Тема: '.$this->arResult['THEME']['TITLE'], $server['REQUEST_URI']);
        $APPLICATION->setTitle('Тема: '.$this->arResult['THEME']['TITLE']);

        CJSCore::init(['ui.buttons', "sidepanel", 'bear.file.input', 'ui.list', 'ui.dialogs.messagebox', 'rs.buttons', 'cool.editor']);

        $this->arResult['ADD_MESSAGE_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#discussion_id#', '#message_id#'],[ $this->arParams['VARIABLES']['discussion_id'], 0 ],$this->arParams['URL_TEMPLATES']['discussion.message.edit']);
        $this->arResult['GRID_ID'] = 'workprojects_forum';
    }

    public function executeComponent() {

        $this->definePermission();

        $this->defineContent();

        $this->defineParams();

        $this->IncludeComponentTemplate();
    }

}
