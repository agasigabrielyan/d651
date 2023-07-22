<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Orm;



class RSMainDirectionLInks extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    function onPrepareComponentParams($arParams){

        if(!$arParams['USER_PERMISSIONS'])
            $arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineActivityDirectionsPermission();

        return $arParams;
    }

    public static function getComponentTemplateResultAction($params){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:activity.directions.map",
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


    function getActivityDirections(){

        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['main_directions_item_edit'];

        $this->arResult['MAIN_DIRECTIONS'] = Orm\ActivityDirectionTable::getList([
            'select' => [
                'DESCRIPTION',
                'TITLE',
                'ID',
                'EDIT_LINK',
                'CURATORS',
            ],
            'order' => ['SORT'=> 'asc'],
            'runtime' => [
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE("'.$linkPathern.'", "#main_id#", %s)',
                    ['ID']
                ),
            ]
        ])->fetchAll();

    }

    function getDetailActivityDirections(){

        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item'];
        $linkPathern2 = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_edit'];

        $this->arResult['DETAIL_DIRECTIONS'] = Orm\DetailDirectionTable::getList([
            'select' => [
                'TITLE',
                'ID',
                'ACTIVITY_DIRECTION_ID',
                'LINK',
                'EDIT_LINK',
                'AD_UNION_ID'
            ],
            'order' => ['SORT'=> 'asc'],
            'runtime' => [
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_RS',
                    'REPLACE("'.$linkPathern.'", "#main_id#", %s)',
                    ['ACTIVITY_DIRECTION_ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK',
                    'REPLACE(%s, "#direction_id#", %s)',
                    ['LINK_RS', 'ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_EDIT_RS',
                    'REPLACE("'.$linkPathern2.'", "#main_id#", %s)',
                    ['ACTIVITY_DIRECTION_ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE(%s, "#direction_id#", %s)',
                    ['LINK_EDIT_RS', 'ID']
                ),
            ]
        ])->fetchAll();

    }

    function checkUpdates(){

        $documentIds = Orm\DetailDirectionDocumentTable::getList([
            'select' => ['ID', 'DETAIL_DIRECTION_ID'],
            'filter' => [
                'DETAIL_DIRECTION_ID' => array_column($this->arResult['DETAIL_DIRECTIONS'], 'ID')
            ]
        ])->fetchAll();

        $eventIds = Orm\DetailDirectionEventTable::getList([
            'select' => ['ID', 'DETAIL_DIRECTION_ID'],
            'filter' => [
                'DETAIL_DIRECTION_ID' => array_column($this->arResult['DETAIL_DIRECTIONS'], 'ID')
            ]
        ])->fetchAll();

        $importantIds = Orm\DetailDirectionImportantTable::getList([
            'select' => ['ID', 'DETAIL_DIRECTION_ID'],
            'filter' => [
                'DETAIL_DIRECTION_ID' => array_column($this->arResult['DETAIL_DIRECTIONS'], 'ID')
            ]
        ])->fetchAll();

        $orderIds = Orm\DetailDirectionOrderTable::getList([
            'select' => ['ID', 'DETAIL_DIRECTION_ID'],
            'filter' => [
                'DETAIL_DIRECTION_ID' => array_column($this->arResult['DETAIL_DIRECTIONS'], 'ID')
            ]
        ])->fetchAll();

        $adsIds = Orm\AdItemTable::getList([
            'select' => ['ID', 'UNION_ID'],
            'filter' => [
                'UNION_ID' => array_column($this->arResult['DETAIL_DIRECTIONS'], 'AD_UNION_ID')
            ]
        ])->fetchAll();

        global $USER;
        $updatesRS = Orm\EntityUpdateTable::getList([
            'filter' => [
                'USER.VALUE' => $USER->getId(),
                [
                    'LOGIC' => 'OR',
                    [
                        'ENTITY_TYPE' => 'DetailDirectionDocument',
                        'ENTITY_ID' => array_column($documentIds, 'ID'),
                    ],
                    [
                        'ENTITY_TYPE' => 'DetailDirectionEvent',
                        'ENTITY_ID' => array_column($eventIds, 'ID'),
                    ],
                    [
                        'ENTITY_TYPE' => 'DetailDirectionImportant',
                        'ENTITY_ID' => array_column($importantIds, 'ID'),
                    ],
                    [
                        'ENTITY_TYPE' => 'DetailDirectionOrder',
                        'ENTITY_ID' => array_column($orderIds, 'ID'),
                    ],
                    [
                        'ENTITY_TYPE' => 'AdItem',
                        'ENTITY_ID' => array_column($adsIds, 'ID'),
                    ]
                ]
            ] 
        ]);
        while($update = $updatesRS->fetch()){
            switch($update['ENTITY_TYPE']){
                case "DetailDirectionDocument":
                    $key = array_search($update['ENTITY_ID'], array_column($documentIds, 'ID'));
                    $directionId = $documentIds[$key]['DETAIL_DIRECTION_ID'];
                    break;

                case "DetailDirectionEvent":
                    $key = array_search($update['ENTITY_ID'], array_column($eventIds, 'ID'));
                    $directionId = $eventIds[$key]['DETAIL_DIRECTION_ID'];
                    break;

                case "DetailDirectionImportant":
                    $key = array_search($update['ENTITY_ID'], array_column($importantIds, 'ID'));
                    $directionId = $importantIds[$key]['DETAIL_DIRECTION_ID'];
                    break;

                case "DetailDirectionOrder":
                    $key = array_search($update['ENTITY_ID'], array_column($orderIds, 'ID'));
                    $directionId = $orderIds[$key]['DETAIL_DIRECTION_ID'];
                    break;

                case "AdItem":
                    $key = array_search($update['ENTITY_ID'], array_column($orderIds, 'ID'));
                    $unionId = $orderIds[$key]['UNION_ID'];
                    $directionKey = array_search($unionId, array_column($this->arResult['DETAIL_DIRECTIONS'], 'AD_UNION_ID'));
                    $directionId = $this->arResult['DETAIL_DIRECTIONS'][$directionKey]['ID'];
                    break;
            }

            $directionKey = array_search($directionId, array_column($this->arResult['DETAIL_DIRECTIONS'], 'ID'));
            $this->arResult['DETAIL_DIRECTIONS'][$directionKey]['IS_NEW'] = true;

            $mainKey = array_search($this->arResult['DETAIL_DIRECTIONS'][$directionKey]['ACTIVITY_DIRECTION_ID'], array_column($this->arResult['MAIN_DIRECTIONS'], 'ID'));
            $this->arResult['MAIN_DIRECTIONS'][$mainKey]['IS_NEW'] = true;
            $updates[] = $update;
        }

        //Entity\Updates\Writer::unsetUserNews($updates);
    }

    function getActiveDirection(){

        $request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $this->arResult['activeDirection']=$request->getCookieRaw('activeDirection') ?? current($this->arResult['MAIN_DIRECTIONS'])['ID'];


        $activeKey = array_search($this->arResult['activeDirection'], array_column($this->arResult['MAIN_DIRECTIONS'], 'ID'));

        $this->arResult['ACTIVE_DIRECTION'] = $this->arResult['MAIN_DIRECTIONS'][$activeKey];
    }

    function defineParams(){
        CJSCore::Init(['sidepanel', 'ui.buttons', 'ui.buttons.icons', 'cool.editor', 'rs.buttons']);
        $this->arResult['DETAIL_ADD_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#direction_id#'], [0], $this->arParams['URL_TEMPLATES']['detail_direction_item_edit']);
        $this->arResult['MAIN_ADD_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#'], [0], $this->arParams['URL_TEMPLATES']['main_directions_item_edit']);
        $this->arResult['SETTINGS'] = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['settings'];
    }

    public function executeComponent() {

        $this->getActivityDirections();
        $this->getDetailActivityDirections();
        $this->getActiveDirection();
        $this->checkUpdates();
        $this->defineParams();

        $this->IncludeComponentTemplate();
    }

}
