<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm;



class RSActivityDirectionImportants extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:activity.directions.item.importants.preview",
            "",
            [
                'SEF_FOLDER' => $params['SEF_FOLDER'],
                'URL_TEMPLATES' => $params['URL_TEMPLATES'],
                'USER_PERMISSIONS' => $params['USER_PERMISSIONS'],
                'VARIABLES' => $params['VARIABLES'],
                'DIRECTION_DATA' => $params['DIRECTION_DATA'],
                'DIRECTION_LINK' => $params['DIRECTION_LINK'],
            ]
        );

        return ob_get_clean();
    }

    function defineImportants(){
        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_important_item_edit'];


        $this->arResult['IMPORTANTS'] = Orm\DetailDirectionImportantTable::getList([
            'filter' => ['DETAIL_DIRECTION_ID' => $this->arParams['VARIABLES']['direction_id']],
            'select' => ['*', 'PREVIEW_PICTURE_PATH', 'EDIT_LINK'],
            'order' => [
                'ID' => 'desc',
            ],
            'limit' => 1,
            'runtime' => [
                'PICTURE' => [
                    'data_type' => 'Bitrix\Main\FileTable',
                    'reference' => [
                        'this.FILE' => 'ref.ID'
                    ]
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'PREVIEW_PICTURE_PATH',
                    'CONCAT("/upload/", %s, "/", %s)',
                    ['PICTURE.SUBDIR', 'PICTURE.FILE_NAME']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_RS_1',
                    'REPLACE("'.$linkPathern.'", "#main_id#", "'.$this->arParams['VARIABLES']['main_id'].'")',
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_RS',
                    'REPLACE(%s, "#direction_id#", "'.$this->arParams['VARIABLES']['direction_id'].'")',
                    ['LINK_RS_1']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE(%s, "#important_id#", %s)',
                    ['LINK_RS', 'ID']
                ),
            ]
        ])->fetchAll();
    }

    function checkUpdates(){

        $updates=[];
        global $USER;
        $updatesRS = Orm\EntityUpdateTable::getList([
            'filter' => [
                'USER.VALUE' => $USER->getId(),
                'ENTITY_TYPE' => 'DetailDirectionImportant',
                'ENTITY_ID' => array_column($this->arResult['IMPORTANTS'], 'ID'),
            ] 
        ]);
        while($update = $updatesRS->fetch()){
            $key = array_search($update["ID"], array_column($this->arResult['IMPORTANTS'], 'ID'));
            $this->arResult['IMPORTANTS'][$key]['IS_NEW'] = true;
            $this->arResult['IMPORTANTS'][$key]['NEW_ID'] = $update['ID'];
            $updates[] = $update;
        }
    }

    function defineParams(){
        CJSCore::Init('sidepanel');
        $this->arResult['AD_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#', '#important_id#'], [$this->arParams['VARIABLES']['main_id'], $this->arParams['VARIABLES']['direction_id'], 0], $this->arParams['URL_TEMPLATES']['detail_direction_item_important_item_edit']);
    }


    public function executeComponent() {

        if(!Loader::IncludeModule("gpi.workproject"))
            return;

        $this->defineImportants();
        $this->defineParams();
        $this->checkUpdates();

        $this->arResult['GRID_ID'] = 'rs_activity_direction_importants';


        $this->IncludeComponentTemplate();
    }

}
