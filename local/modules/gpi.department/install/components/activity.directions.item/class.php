<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm;



class RSActivityDirectionsItem extends  \CBitrixComponent implements Controllerable{
    public function configureActions(){}

    public static function getComponentTemplateResultAction($params){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:activity.directions.item",
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


    function defineDirectionData(){

        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_edit'];

        $this->arResult['DIRECTION'] = Orm\DetailDirectionTable::getList([
            'filter' => ['ID' => $this->arParams['VARIABLES']['direction_id']],
            'select' => ['MAIN_' => 'MAIN_DIRECTION', '*', 'EDIT_LINK'],
            'order' => ['ID' => 'desc'],
            'runtime' => [
                'MAIN_DIRECTION' => [
                    'data_type' => '\Gpi\Workproject\Orm\ActivityDirectionTable',
                    'reference' => ['this.ACTIVITY_DIRECTION_ID' => 'ref.ID'],
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_RS',
                    'REPLACE("'.$linkPathern.'", "#main_id#", %s)',
                    ['ACTIVITY_DIRECTION_ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE(%s, "#direction_id#", %s)',
                    ['LINK_RS', 'ID']
                ),
            ]
        ])->fetch();
    }

    function defineProps(){
        CJSCore::init(["sidepanel", 'cool.editor']);
        $this->arResult['GRID_ID'] = 'rs_activity_directions_item';
    }

    public function executeComponent() {

        if(!Loader::IncludeModule("gpi.workproject"))
            return;

        
        $this->defineDirectionData();
        
        $this->defineProps();

        $this->IncludeComponentTemplate();
        global $APPLICATION;
        $APPLICATION->setTitle($this->arResult['DIRECTION']['TITLE']);
    }

}
