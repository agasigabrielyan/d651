<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Loader,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper,
    Gpi\Workproject\Orm;

class RSEntityStructure extends  \CBitrixComponent implements Controllerable{
    protected $entityCommentsTable = 'Gpi\Workproject\Orm\EntityCommentsTable';
    function configureActions(){}

    public static function loadEntityAction(){

        if(!Loader::includeModule("gpi.workproject"))
            return;

        return Helper\FormData::save(new Orm\EntityCommentsTable(), [], 1);
    }

    public static function deleteEntityAction($id){
        if(!Loader::includeModule("gpi.workproject"))
            return;

        return Orm\EntityCommentsTable::delete($id)->isSuccess();
    }

    public static function getComponentTemplateResultAction($params){
        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:entity.comments",
            "",
            $params
        );

        return ob_get_clean();
    }

    function defineProps(){
        CJSCore::init(['bear.file.input', 'sidepanel']);
        $this->arResult['DELETE_BTN'] = Entity\EditorManager::DELETE_SVG;
        $this->arResult['EDIT_BTN'] = Entity\EditorManager::EDIT_SVG;

        global $APPLICATION;
        $APPLICATION->AddHeadString("
        <script> 
            window.entityCommentsParams = ".Bitrix\Main\Web\Json::encode($this->arParams).";
        </script>
        ");
    }

    function defineComments(){
        $commentsRS = $this->entityCommentsTable::getList([
            'filter' => [
                'ENTITY' => $this->arParams['ENTITY'],
                'ENTITY_ID' => $this->arParams['ENTITY_ID'],
            ],
            'select' => ['ENTITY', 'ENTITY_ID', 'ID', 'CREATED_BY', 'BODY', 'FILES', 'CREATOR_FIO', 'CREATED_TIME', 'UPDATED_TIME'],
            'runtime' => [
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'CREATOR_LAST_NAME',
                    'COALESCE(%s, " ")',
                    'USER.LAST_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'CREATOR_NAME',
                    'COALESCE(%s, " ")',
                    'USER.NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'CREATOR_SECOND_NAME',
                    'COALESCE(%s, " ")',
                    'USER.SECOND_NAME'
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'CREATOR_FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['CREATOR_LAST_NAME','CREATOR_NAME', 'CREATOR_SECOND_NAME']
                ),
            ]
        ])->fetchAll();

        $filesRS = array_column($commentsRS, 'FILES');
        foreach ($filesRS as $files)
            $filesRSList = array_merge($filesRSList ?? [], $files ?? []);

        if($filesRSList){
            $filesOrmRS = Bitrix\Main\FileTable::getList([
                'filter' => [
                    'ID' => $filesRSList
                ],
                'select' => ['*', 'TITLE' => 'ORIGINAL_NAME', 'LINK'],
                'runtime' => [
                    new \Bitrix\Main\Entity\ExpressionField(
                        'LINK',
                        'CONCAT("/upload/", %s, "/", %s)',
                        ['SUBDIR', 'FILE_NAME']
                    ),
                ]
            ]);
            while($file = $filesOrmRS->fetch()){
                $fileList[$file['ID']] = $file;
            }
        }

        foreach ($commentsRS as $comment){
            $comment['FILES'] = array_map(function($v) use ($fileList){
                return $fileList[$v];
            }, $comment['FILES']);

            foreach (array_filter($comment, fn($v) => $v instanceOf Bitrix\Main\Type\DateTime || $v instanceOf Bitrix\Main\Type\Date) as $dateKey => $timeObject){
                if($timeObject->format('H:i') != '00:00')
                    $comment[$dateKey] = $timeObject->format('d.m.Y H:i:s');
                else
                    $comment[$dateKey] = $timeObject->format('d.m.Y');
            }

            $this->arResult['COMMENTS'][] = $comment;
        }
    }

    public function executeComponent() {

        if(!Loader::IncludeModule('gpi.workproject'))
            return;

        $this->defineProps();
        $this->defineComments();

        $this->IncludeComponentTemplate();
    }
}
