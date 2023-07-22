<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Loader,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Entity,
    Gpi\Workproject\Helper;


class AdUnionList extends  \CBitrixComponent implements Controllerable{

    function onPrepareComponentParams($params){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$params['USER_PERMISSIONS'])
            $params['USER_PERMISSIONS'] = Entity\EditorManager::defineAdPermission($params['UNION_ID']);


        return $params;
    }

    public function configureActions(){}
    public static function getComponentTemplateResultAction($params, $adLike=false){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:ad.union.list",
            "",
            array_merge($params, [
                'ad_like' => $adLike,
            ])
        );

        return ob_get_clean();
    }

    public static function renameAdUnionAction($id, $title){
        Orm\AdUnionTable::update($id, ['TITLE' => $title]);
    }

    function defineAds(){

        Loader::IncludeModule('gpi.workproject');
        $projectAdsTable = new Orm\AdItemTable();

        $filter = ['UNION_ID' =>  $this->arParams['UNION_ID'],];

        if($this->arParams['ad_like'])
            $filter[] = [
                'LOGIC' => 'OR',
                'DESCRIPTION' => '%'.$this->arParams['ad_like'].'%',
                'TITLE' => '%'.$this->arParams['ad_like'].'%',
            ];

        $query = [
            'select' => ['*', 'FIO'],
            'runtime' => [
                'USER' => [
                    'data_type' => 'Bitrix\Main\UserTable',
                    'reference' => [
                        'this.CREATED_BY' => 'ref.ID',
                    ]
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'FIO',
                    'CONCAT(%s, " ", %s, " ", %s)',
                    ['USER.LAST_NAME','USER.NAME', 'USER.SECOND_NAME']
                ),
            ],
            'filter' => $filter,
        ];

        if($this->arParams['MAX_LIST_ITEMS'])
            $query['limit'] = $this->arParams['MAX_LIST_ITEMS'];

        $adsRs = $projectAdsTable::getList($query)->fetchAll();

        $filesIds = array_filter(array_column($adsRs, 'PREVIEW'));
        foreach ($adsRs as $ad){
            $filesIds = array_merge($filesIds ?? [], is_array($ad['FILES']) ? $ad['FILES'] : []);
        }

        $filesListRS = Bitrix\Main\FileTable::getList(['filter' => ['ID' => $filesIds]]);

        while($file = $filesListRS->fetch()){
            $file['SRC'] = '/upload/'.$file['SUBDIR'].'/'.$file['FILE_NAME'];
            $fileList[$file['ID']] = $file;
        }

        foreach ($adsRs as $ad){

            $ad['FILES'] = array_map(function($v) use ($fileList) {
                return $fileList[$v];
            }, $ad['FILES']);
            $ad['PREVIEW'] = $fileList[$ad['PREVIEW']];
            $ad['LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#ad_id#'], [$ad['ID']], $this->arParams['URL_TEMPLATES']['item']);
            $ad['EDIT_LINK'] = $this->arParams['SEF_FOLDER'].str_replace(['#ad_id#'], [$ad['ID']], $this->arParams['URL_TEMPLATES']['item.edit']);

            $this->arResult['LIST'][$ad['ID']] = $ad;
        }


    }

    function defineParams(){
        global $APPLICATION;
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        if(!$this->arParams['ad_like'])
            $this->arParams['ad_like'] = $request->getCookieRaw('ad_like');
        $this->arResult['SETTINGS_PATH'] = $this->arParams['SEF_FOLDER'].'settings/';
        $this->arResult['CREATE_AD_PATH'] = $this->arParams['SEF_FOLDER']. str_replace(['#ad_id#'], [0], $this->arParams['URL_TEMPLATES']['item.edit']);
        $this->arResult['GRID_PARAMS']['ID'] = 'ads_list_'.$this->arParams['UNION_ID'];

        $APPLICATION->AddChainItem('Объявления', $this->arParams['SEF_FOLDER']);
        $APPLICATION->setTitle('Объявления');

        CJSCore::init(['sidepanel', 'ui.dialogs.messagebox', 'rs.buttons', 'ui.list', 'ui.buttons', 'ui.buttons.icons', 'cool.editor']);
    }

    function checkUpdates(){

        global $USER;
        $updatesRS = Orm\EntityUpdateTable::getList([
            'filter' => [
                'USER.VALUE' => $USER->getId(),
                'ENTITY_TYPE' => 'AdItem',
                'ENTITY_ID' => array_column($this->arResult['LIST'], 'ID'),
            ]
        ]);
        while($update = $updatesRS->fetch()){
            $this->arResult['LIST'][$update["ENTITY_ID"]]['IS_NEW'] = true;
            $this->arResult['LIST'][$update["ENTITY_ID"]]['NEW_ID'] = $update['ID'];
        }
    }

    public function definePermission(){

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineAdPermission($this->arParams['UNION_ID']);

        if(array_intersect(['R', 'W', 'X'], $this->arParams['USER_PERMISSIONS']) )
            return true;

        header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
    }

    public function executeComponent() {

        $this->definePermission();
        $this->defineParams();
        $this->defineAds();
        $this->checkUpdates();


        $this->IncludeComponentTemplate($this->arParams['TEMPLATE_PAGE']);
    }

}
