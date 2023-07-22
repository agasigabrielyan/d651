<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Loader,
    \Bitrix\Main\Application,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Helper,
    Gpi\Workproject\Entity;



class RSGalleryAlbums extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    function onPrepareComponentParams($arParams){
        if(!Loader::IncludeModule("gpi.workproject"))
            return;

        if(!$arParams['USER_PERMISSIONS'])
            $arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineGalleryPermission($arParams['GALLERY_ID']);

        return $arParams;
    }

    public static function getComponentTemplateResultAction($params, $docLike=false){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:gallery.albums",
            "",
            [
                'SEF_FOLDER' => $params['SEF_FOLDER'],
                'URL_TEMPLATES' => $params['URL_TEMPLATES'],
                'USER_PERMISSIONS' => $params['USER_PERMISSIONS'],
                'VARIABLES' => $params['VARIABLES'],
                'GALLERY_ID' => $params['GALLERY_ID'],
            ]
        );

        return ob_get_clean();
    }

    public static function renameGalleryAction($id, $title){
        Orm\GalleryTable::update($id, ['TITLE' => $title]);
    }

    public static function loadEntityAction(){
        if (!Loader::includeModule("gpi.workproject"))
            return;

        return Helper\FormData::save(new Orm\GalleryAlbumTable(), [], 1);
    }
    public static function deleteEntityAction($id){
        if (!Loader::includeModule("gpi.workproject"))
            return;
        return Orm\GalleryAlbumTable::delete($id);
    }

    function defineAlbums(){
        $detailAlbulLinkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['album'];

        $this->arResult['ALBUMS'] = Orm\GalleryAlbumTable::getList([
            'filter' => [
                'GALLERY_ID' => $this->arParams['GALLERY_ID']
            ],
            'select' => ['*', 'PREVIEW_FILE_' => 'PREVIEW_FILE.*', 'PREVIEW_LINK', 'ALBUM_LINK'],
            'runtime' => [
                'PREVIEW_FILE' => [
                    'data_type'=> '\Bitrix\Main\FileTable',
                    'reference' => [
                        'this.PREVIEW' => 'ref.ID'
                    ]
                ],
                new \Bitrix\Main\Entity\ExpressionField(
                    'PREVIEW_LINK',
                    'CONCAT("/upload/", %s, "/", %s)',
                    ['PREVIEW_FILE.SUBDIR', 'PREVIEW_FILE.FILE_NAME']
                ),
                new \Bitrix\Main\Entity\ExpressionField(
                    'ALBUM_LINK',
                    'REPLACE("'.$detailAlbulLinkPathern.'", "#album_id#", %s)',
                    ['ID']
                ),
            ]
        ])->fetchAll();
    }
    function defineProps(){
        CJSCore::init(["sidepanel", 'ui.dialogs.messagebox', 'bear.file.input', 'ui.buttons', 'ui.buttons.icons', 'cool.editor']);
        $this->arResult['GRID_ID'] = 'rs_gallery_albums';
        $this->arResult['DELETE_BTN'] = Entity\EditorManager::DELETE_SVG;
        $this->arResult['EDIT_BTN'] = Entity\EditorManager::EDIT_SVG;


        global $APPLICATION;

        $APPLICATION->addHeadString("
        <script>
            window.rsGalleryParams = ".Bitrix\Main\Web\Json::encode($this->arParams).";
        </script>
        ");
    }

    function checkUpdates(){

        global $USER;
        $updates = Orm\GalleryAlbumItemTable::getList([
            'filter' => [
                'ALBUM_ID' => array_column($this->arResult['ALBUMS'], 'ID'),
                'NEW.USER.VALUE' => $USER->getId(),
                'NEW.ENTITY_TYPE' => 'GalleryAlbumItem',
            ],
            'select' => ['ALBUM_ID', 'UPDATE_ID' => 'NEW.ID'],
            'runtime' => [
                'NEW' => [
                    'data_type'=> 'Gpi\Workproject\Orm\EntityUpdateTable',
                    'reference' => [
                        'this.ID' => 'ref.ENTITY_ID'
                    ]
                ],
            ]
        ]);
        while($update = $updates->fetch()){
            $key = array_search($update["ALBUM_ID"], array_column($this->arResult['ALBUMS'], 'ID'));
            $this->arResult['ALBUMS'][$key]['IS_NEW'] = true;
            $this->arResult['ALBUMS'][$key]['NEW_ID'] = $update['UPDATE_ID'];
        }

    }

    public function checkPermission(){

        global $USER;

        if (!Loader::includeModule("gpi.workproject"))
            return;

        if(!$this->arParams['USER_PERMISSIONS'])
            $this->arParams['USER_PERMISSIONS'] = Entity\EditorManager::defineGalleryPermission($this->arParams['GALLERY_ID']);

        if(array_intersect(['X', 'W', 'R'], $this->arParams['USER_PERMISSIONS']) )
            return true;

        header('Location: '.$this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['locked']);
    }

    public function executeComponent() {

        $this->checkPermission();
        $this->defineProps();
        $this->defineAlbums();
        $this->checkUpdates();

        $this->IncludeComponentTemplate();
    }

}
