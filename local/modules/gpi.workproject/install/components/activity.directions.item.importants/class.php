<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Gpi\Workproject\Orm,
    \Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable;



class RSActivityDirectionImportants extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params, $importantsLike=false)
    {

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:activity.directions.item.importants",
            "",
            [
                'SEF_FOLDER' => $params['SEF_FOLDER'],
                'URL_TEMPLATES' => $params['URL_TEMPLATES'],
                'USER_PERMISSIONS' => $params['USER_PERMISSIONS'],
                'VARIABLES' => $params['VARIABLES'],
                'DIRECTION_DATA' => $params['DIRECTION_DATA'],
                'DIRECTION_LINK' => $params['DIRECTION_LINK'],
                'importants_like' => $importantsLike,
            ]
        );

        return ob_get_clean();
    }

    function defineImportants(){

        $filter = [];

        if($this->arParams['importants_like'])
            $filter[] = [
                'LOGIC' => 'OR',
                'DESCRIPTION' => '%'.$this->arParams['importants_like'].'%',
                'TITLE' => '%'.$this->arParams['importants_like'].'%',
                'MAIN_DIRECTION_TITLE' => '%'.$this->arParams['importants_like'].'%',
            ];

        if($this->arParams['VARIABLES']['direction_id'])
            $filter['DETAIL_DIRECTION_ID'] = $this->arParams['VARIABLES']['direction_id'];

        $linkPathern2 = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_important_item'];
        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_important_item_edit'];

        $this->arResult['IMPORTANTS'] = Orm\DetailDirectionImportantTable::getList([
            'filter' => $filter,
            'select' => ['*', 'DETAIL_DIRECTION_TITLE' => 'DETAIL_DIRECTION.TITLE', 'MAIN_DIRECTION_TITLE' => 'MAIN_DIRECTION.TITLE', 'LINK', 'EDIT_LINK'],
            'runtime' => [
                'DETAIL_DIRECTION' => [
                    'data_type' => 'Gpi\Workproject\Orm\DetailDirectionTable',
                    'reference' => [
                        'this.DETAIL_DIRECTION_ID' => 'ref.ID'
                    ]
                ],
                'MAIN_DIRECTION' => [
                    'data_type' => 'Gpi\Workproject\Orm\ActivityDirectionTable',
                    'reference' => [
                        'this.DETAIL_DIRECTION.ACTIVITY_DIRECTION_ID' => 'ref.ID'
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_RS_1',
                    'REPLACE("'.$linkPathern.'", "#main_id#", %s)',
                    ['MAIN_DIRECTION.ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_RS',
                    'REPLACE(%s, "#direction_id#", %s)',
                    ['LINK_RS_1', 'DETAIL_DIRECTION_ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE(%s, "#important_id#", %s)',
                    ['LINK_RS', 'ID']
                ),

                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_2_RS_1',
                    'REPLACE("'.$linkPathern2.'", "#main_id#", %s)',
                    ['MAIN_DIRECTION.ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK_2_RS',
                    'REPLACE(%s, "#direction_id#", %s)',
                    ['LINK_2_RS_1', 'DETAIL_DIRECTION_ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'LINK',
                    'REPLACE(%s, "#important_id#", %s)',
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
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        if(!$this->arParams['importants_like'])
            $this->arParams['importants_like'] = $request->getCookieRaw('importants_like');

        $this->arResult['AD_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#', '#important_id#'], [$this->arParams['VARIABLES']['main_id'], $this->arParams['VARIABLES']['direction_id'], 0], $this->arParams['URL_TEMPLATES']['detail_direction_item_important_item_edit']);
        $this->arResult['GRID_ID'] = 'rs_activity_direction_importants';
    }


    public function executeComponent() {

        if(!Loader::IncludeModule("gpi.workproject"))
            return;

        $this->defineParams();

        $this->defineImportants();;
        $this->checkUpdates();

        $this->IncludeComponentTemplate();
    }

}
