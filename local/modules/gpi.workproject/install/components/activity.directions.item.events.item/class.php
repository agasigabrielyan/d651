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
            "rs:activity.directions.item.events.item",
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


        $this->arResult['EVENT'] = Orm\DetailDirectionEventTable::getList([
            'filter' => ['ID' => $this->arParams['VARIABLES']['event_id']],
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
                    ['LINK_RS_1']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK',
                    'REPLACE(%s, "#event_id#", %s)',
                    ['LINK_RS_1', 'ID']
                ),
            ]
        ])->fetch();
    }

    function checkUpdates(){

        $updates=[];
        global $USER;
        $updatesRS = Orm\EntityUpdateTable::getList([
            'filter' => [
                'USER.VALUE' => $USER->getId(),
                'ENTITY_TYPE' => 'DetailDirectionEvent',
                'ENTITY_ID' => $this->arResult['EVENT']['ID']),
            ] 
        ]);
        while($update = $updatesRS->fetch()){
            $this->arResult['EVENT']['IS_NEW'] = true;
            $this->arResult['EVENT']['NEW_ID'] = $update['ID'];
            $updates[] = $update;
        }
    }

    function defineParams(){

        $this->arResult['GRID_ID'] = 'rs_activity_direction_events';
        CJSCore::Init(['sidepanel.reference.link.save', 'sidepanel', 'cool.editor']);

        $this->arResult['LIST_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#'], [$this->arParams['VARIABLES']['main_id'], $this->arParams['VARIABLES']['direction_id']], $this->arParams['URL_TEMPLATES']['detail_direction_item_events']);
        $this->arResult['AD_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#', '#event_id#'], [$this->arParams['VARIABLES']['main_id'], $this->arParams['VARIABLES']['direction_id'], 0], $this->arParams['URL_TEMPLATES']['detail_direction_item_events_item_edit']);
    }

    public function executeComponent() {

        if(!Loader::IncludeModule("gpi.workproject"))
            return;

        $this->defineEventsData();
        $this->defineParams();
        $this->checkUpdates();

        $this->IncludeComponentTemplate();
    }

}
