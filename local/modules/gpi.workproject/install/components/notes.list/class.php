<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Bitrix\Main\Engine\Contract\Controllerable;


class RsNotesList extends  \CBitrixComponent implements Controllerable{

    function configureActions(){}

    public static function getComponentTemplateResultAction($params){
        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:notes.list",
            "",
            $params
        );

        return ob_get_clean();
    }

    function defineProps(){

        CJSCore::init(['sidepanel','cool.editor']);

        $this->arResult['AD_LINK'] = $this->arParams['SEF_FOLDER'].str_replace('#note_id#', 0, $this->arParams['URL_TEMPLATES']['item.edit']);

        global $APPLICATION;
        $APPLICATION->AddHeadString("
        <script> 
            const notesListParams = ".Bitrix\Main\Web\Json::encode($this->arParams).";
        </script>
        ");
    }

    function defineNotes(){
        global $USER;

        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['item'];
        $linkPathern2 = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['item.edit'];

        $this->arResult['NOTES'] = Orm\NotesTable::getList([
            'select' => ['*', 'EDIT_LINK', 'LINK'],
            'filter' => [
                //'CREATED_BY' => $USER->getId()
            ],
            'runtime' => [
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE("'.$linkPathern2.'", "#note_id#", %s)',
                    ['ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK',
                    'REPLACE("'.$linkPathern.'", "#note_id#", %s)',
                    ['ID']
                ),
            ]
        ])->fetchAll();
    }

    public function executeComponent() {

        $this->defineProps();

        $this->defineNotes();

        $this->IncludeComponentTemplate();
    }

}
