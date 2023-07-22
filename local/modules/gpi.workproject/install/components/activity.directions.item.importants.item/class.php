<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Gpi\Workproject\Orm,
    \Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable;



class RSActivityDirectionImportantsItem extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params)
    {

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:activity.directions.item.importants.item",
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

    function defineImportant(){

        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_important_item_edit'];
        $linkPathern2 = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_important_item'];

        $this->arResult['IMPORTANT'] = Orm\DetailDirectionImportantTable::getList([
            'filter' => [
                'ID' => $this->arParams['VARIABLES']['important_id']
            ],
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
        ])->fetch();
    }

    function checkUpdates(){

        $updates=[];
        global $USER;
        $updatesRS = Orm\EntityUpdateTable::getList([
            'filter' => [
                'USER.VALUE' => $USER->getId(),
                'ENTITY_TYPE' => 'DetailDirectionImportant',
                'ENTITY_ID' => $this->arResult['IMPORTANT']['ID']),
            ] 
        ]);
        while($update = $updatesRS->fetch()){
            $this->arResult['IMPORTANT']['IS_NEW'] = true;
            $this->arResult['IMPORTANT']['NEW_ID'] = $update['ID'];
            $updates[] = $update;
        }
    }


    public function executeComponent() {

        if(!Loader::IncludeModule("gpi.workproject"))
            return;

        $this->defineImportant();
        $this->checkUpdates();

        $this->arResult['GRID_ID'] = 'rs_activity_direction_ads';
        CJSCore::Init(['sidepanel.reference.link.save', 'cool.editor']);

        $this->IncludeComponentTemplate();
    }

}
