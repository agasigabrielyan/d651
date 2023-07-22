<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm;



class RSActivityDirectionDocs extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params, $docLike=false){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:activity.directions.item.docs.preview",
            "",
            [
                'SEF_FOLDER' => $params['SEF_FOLDER'],
                'URL_TEMPLATES' => $params['URL_TEMPLATES'],
                'USER_PERMISSIONS' => $params['USER_PERMISSIONS'],
                'VARIABLES' => $params['VARIABLES'],
                'DIRECTION_DATA' => $params['DIRECTION_DATA'],
                'DIRECTION_LINK' => $params['DIRECTION_LINK'],
                'docs_like' => $docLike,
            ]
        );

        return ob_get_clean();
    }

    function defineDocsData(){

        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_order_item_edit'];

        $filter = ['DETAIL_DIRECTION_ID' => $this->arParams['VARIABLES']['direction_id']];

        if($this->arParams['docs_like'])
            $filter[] = [
                'LOGIC' => 'OR',
                'TITLE' => '%'.$this->arParams['docs_like'].'%',
                'ORDER_NUMBER' => '%'.$this->arParams['docs_like'].'%',
            ];

        $this->arResult['RULLS'] = Orm\DetailDirectionOrderTable::getList([
            'filter' => $filter,
            'select' => ['*', 'FILE_PATH', 'EDIT_LINK'],
            'order' => ['ORDER_DATE' => 'desc'],
            'limit' => 4,
            'runtime' => [
                'PICTURE' => [
                    'data_type' => 'Bitrix\Main\FileTable',
                    'reference' => [
                        'this.FILE' => 'ref.ID'
                    ]
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'FILE_PATH',
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
                    'REPLACE(%s, "#order_id#", %s)',
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
                'ENTITY_TYPE' => 'DetailDirectionOrder',
                'ENTITY_ID' => array_column($this->arResult['RULLS'], 'ID'),
            ] 
        ]);
        while($update = $updatesRS->fetch()){
            $key = array_search($update["ID"], array_column($this->arResult['RULLS'], 'ID'));
            $this->arResult['RULLS'][$key]['IS_NEW'] = true;
            $this->arResult['RULLS'][$key]['NEW_ID'] = $update['ID'];
        }
    }

    function defineParams(){
        CJSCore::Init('sidepanel', 'cool.editor');
        $this->arResult['LIST_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#'], [$this->arParams['VARIABLES']['main_id'], $this->arParams['VARIABLES']['direction_id']], $this->arParams['URL_TEMPLATES']['detail_direction_item_order']);
        $this->arResult['AD_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#', '#order_id#'], [$this->arParams['VARIABLES']['main_id'], $this->arParams['VARIABLES']['direction_id'], 0], $this->arParams['URL_TEMPLATES']['detail_direction_item_order_item_edit']);


        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        if(!$this->arParams['docs_like'])
            $this->arParams['docs_like'] = $request->getCookieRaw('docs_like');
    }

    public function executeComponent() {

        if(!Loader::IncludeModule("gpi.workproject"))
            return;

        $this->defineParams();
        $this->defineDocsData();
        $this->checkUpdates();
        $this->arResult['GRID_ID'] = 'rs_activity_direction_docs';



        $this->IncludeComponentTemplate();
    }

}
