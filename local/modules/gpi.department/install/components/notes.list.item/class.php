<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Helper,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Entity\UrlManager,
    Bitrix\Main\Engine\Contract\Controllerable;


class RsTasksListItem extends \CBitrixComponent  implements Controllerable{

    public function configureActions(){}

    function defineNote(){
        $this->arResult = Orm\NotesTable::getById($this->arParams['VARIABLES']['note_id'])->fetch();

        if($this->arResult['FILES']){
            $this->arResult['FILES']= Bitrix\Main\FilesTable::getList([
                'filter' => ['ID' => $this->arResult['FILES']],
                'select' => [
                    '*', 'FILE_PATH',
                ],
                'runtime' => [
                    new Bitrix\Main\Entity\ExpressionField(
                        'FILE_PATH',
                        'CONCAT("/upload/", %s, "/", %s)',
                        ['SUBDIR', 'FILE_NAME']
                    ),
                ]
            ])->fetchAll();
        }
    }

    public function executeComponent() {
        $this->defineNote();
        $this->IncludeComponentTemplate();
    }

}
