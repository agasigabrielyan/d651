<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm;



class RSActivityDirectionEvents extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:activity.directions.item.events.preview",
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

    function defineEventsData(){

        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_events_item_edit'];
        $linkPathern2 = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_events_item'];


        $this->arResult['EVENTS'] = Orm\DetailDirectionEventTable::getList([
            'filter' => ['DETAIL_DIRECTION_ID' => $this->arParams['VARIABLES']['direction_id']],
            'select' => ['*', 'PREVIEW_PICTURE_PATH', 'EDIT_LINK', 'LINK'],
            'order' => [
                'DATE' => 'desc',
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
                    'REPLACE(%s, "#event_id#", %s)',
                    ['LINK_RS', 'ID']
                ),

                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_2_RS_1',
                    'REPLACE("'.$linkPathern2.'", "#main_id#", "'.$this->arParams['VARIABLES']['main_id'].'")',
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_2_RS',
                    'REPLACE(%s, "#direction_id#", "'.$this->arParams['VARIABLES']['direction_id'].'")',
                    ['LINK_2_RS_1']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK',
                    'REPLACE(%s, "#event_id#", %s)',
                    ['LINK_2_RS', 'ID']
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
                'ENTITY_TYPE' => 'DetailDirectionEvent',
                'ENTITY_ID' => array_column($this->arResult['EVENTS'], 'ID'),
            ] 
        ]);
        while($update = $updatesRS->fetch()){
            $key = array_search($update["ID"], array_column($this->arResult['EVENTS'], 'ID'));
            $this->arResult['EVENTS'][$key]['IS_NEW'] = true;
            $this->arResult['EVENTS'][$key]['NEW_ID'] = $update['ID'];
            $updates[] = $update;
        }
    }

    function defineParams(){
        CJSCore::Init('sidepanel', 'cool.editor');
        $this->arResult['LIST_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#'], [$this->arParams['VARIABLES']['main_id'], $this->arParams['VARIABLES']['direction_id']], $this->arParams['URL_TEMPLATES']['detail_direction_item_events']);
        $this->arResult['AD_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#', '#event_id#'], [$this->arParams['VARIABLES']['main_id'], $this->arParams['VARIABLES']['direction_id'], 0], $this->arParams['URL_TEMPLATES']['detail_direction_item_events_item_edit']);
    }

    public function executeComponent() {

        if(!Loader::IncludeModule("iblock"))
            return;

        $this->defineParams();
        $this->defineEventsData();
        $this->checkUpdates();

        $this->arResult['GRID_ID'] = 'rs_activity_direction_events';

        $this->IncludeComponentTemplate();
    }

}
