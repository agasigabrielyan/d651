<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset,
    Bitrix\Main\Context,
    Bitrix\Main\Loader,
    Bitrix\Main\Type\DateTime,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm;



class RSActivityDirectionLocalRulls extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params, $localLike=false){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:activity.directions.item.local.rulls",
            $params['TEMPLATE_NAME'],
            [
                'SEF_FOLDER' => $params['SEF_FOLDER'],
                'URL_TEMPLATES' => $params['URL_TEMPLATES'],
                'USER_PERMISSIONS' => $params['USER_PERMISSIONS'],
                'VARIABLES' => $params['VARIABLES'],
                'DIRECTION_DATA' => $params['DIRECTION_DATA'],
                'DIRECTION_LINK' => $params['DIRECTION_LINK'],
                'local_like' => $localLike
            ]
        );

        return ob_get_clean();
    }

    function defineRullsData(){

        $linkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['detail_direction_item_dicuments_item_edit'];

        $filter = ['DETAIL_DIRECTION_ID' => $this->arParams['VARIABLES']['direction_id']];

        if($this->arParams['local_like'])
            $filter[] = [
                'LOGIC' => 'OR',
                'TITLE' => '%'.$this->arParams['local_like'].'%',
                'DOC_NUMBER' => '%'.$this->arParams['local_like'].'%',
            ];

        $this->arResult['RULLS'] = Orm\DetailDirectionDocumentTable::getList([
            'filter' => $filter,
            'select' => ['*', 'FILE_PATH', 'EDIT_LINK', 'MAIN_DIRECTION_' => 'MAIN_DIRECTION.*', 'DETAIL_DIRECTION__' => 'DETAIL_DIRECTION.*'],
            'order' => ['DOC_DATE' => 'desc'],
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
                    ['LINK_RS_1', 'DETAIL_DIRECTION.ID']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'EDIT_LINK',
                    'REPLACE(%s, "#document_id#", %s)',
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
                'ENTITY_TYPE' => 'DetailDirectionDocument',
                'ENTITY_ID' => array_column($this->arResult['RULLS'], 'ID'),
            ] 
        ]);
        while($update = $updatesRS->fetch()){
            $key = array_search($update["ID"], array_column($this->arResult['RULLS'], 'ID'));
            $this->arResult['RULLS'][$key]['IS_NEW'] = true;
            $this->arResult['RULLS'][$key]['NEW_ID'] = $update['ID'];
            $updates[] = $update;
        }
    }

    function defineParams(){
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        if(!$this->arParams['local_like'])
            $this->arParams['local_like'] = $request->getCookieRaw('local_like');

        CJSCore::Init('sidepanel', 'cool.editor');
        $this->arParams['TEMPLATE_NAME'] = $this->getTemplateName();
        $this->arResult['GRID_ID'] = 'rs_activity_direction_local_rulls';
        $this->arResult['LIST_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#'], [$this->arParams['VARIABLES']['main_id'], $this->arParams['VARIABLES']['direction_id']], $this->arParams['URL_TEMPLATES']['detail_direction_item_dicuments']);
        $this->arResult['AD_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#', '#document_id#'], [$this->arParams['VARIABLES']['main_id'], $this->arParams['VARIABLES']['direction_id'], 0], $this->arParams['URL_TEMPLATES']['detail_direction_item_dicuments_item_edit']);
    }

    public function executeComponent() {

        if(!Loader::IncludeModule("gpi.workproject"))
            return;

        $this->defineParams();
        $this->defineRullsData();
        $this->checkUpdates();

        $this->IncludeComponentTemplate();
    }

}
