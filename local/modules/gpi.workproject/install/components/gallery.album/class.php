<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Loader,
    \Bitrix\Main\Application,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Helper,
    Gpi\Workproject\Entity;



class RSGalleryAlbum extends  \CBitrixComponent implements Controllerable{

    public function configureActions(){}

    public static function getComponentTemplateResultAction($params, $docLike=false){

        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "rs:gallery.album",
            "",
            [
                'SEF_FOLDER' => $params['SEF_FOLDER'],
                'URL_TEMPLATES' => $params['URL_TEMPLATES'],
                'USER_PERMISSIONS' => $params['USER_PERMISSIONS'],
                'VARIABLES' => $params['VARIABLES'],
            ]
        );

        return ob_get_clean();
    }

    public static function loadPhotosAction(){

        $request = Application::getInstance()->getContext()->getRequest();
        $data = $request->getPostList()->toArray();
        $files = $request->getFileList()->toArray();

        unset($files['FILES']);
        foreach ($files as $fileHash){
            $result = Orm\GalleryAlbumItemTable::add([
                'TITLE' => $fileHash['name'],
                'FILE' => $fileHash,
                'ALBUM_ID' => $data['ALBUM_ID'],
            ]);
            $elementsIds[] = $result->getId();
        }

        return $elementsIds;

    }

    public static function deletePhotoAction($id){
        return Orm\GalleryAlbumItemTable::delete($id);
    }

    function defineAlbumData(){
        if(!Loader::includeModule("gpi.workproject"))
            return;

        $detailAlbulLinkPathern = $this->arParams['SEF_FOLDER'].$this->arParams['URL_TEMPLATES']['album'];

        $this->arResult['ALBUM'] = Orm\GalleryAlbumTable::getList([
            'filter' => [
                'GALLERY_ID' => $this->arParams['VARIABLES']['album_id']
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
        ])->fetch();

        $this->arResult['PHOTOS'] = Orm\GalleryAlbumItemTable::getList([
            'filter' => ['ALBUM_ID' => $this->arParams['VARIABLES']['album_id']],
            'select' => ['ID', 'TITLE', 'PICTURE_PATH', 'FILE'],
            'order' => ['ID' => 'desc'],
            'runtime' => [
                'PICTURE' => [
                    'data_type' => 'Bitrix\Main\FileTable',
                    'reference' => [
                        'this.FILE' => 'ref.ID'
                    ]
                ],
                new Bitrix\Main\Entity\ExpressionField(
                    'PICTURE_PATH',
                    'CONCAT("/upload/", %s, "/", %s)',
                    ['PICTURE.SUBDIR', 'PICTURE.FILE_NAME']
                )
            ]
        ])->fetchAll();

    }

    function checkUpdates(){

        global $USER;
        $updatesRS = Orm\EntityUpdateTable::getList([
            'filter' => [
                'USER.VALUE' => $USER->getId(),
                'ENTITY_TYPE' => 'GalleryAlbumItem',
                'ENTITY_ID' => array_column($this->arResult['PHOTOS'], 'ID'),
            ]
        ]);
        while($update = $updatesRS->fetch()){
            $key = array_search($update["ENTITY_ID"], array_column($this->arResult['PHOTOS'], 'ID'));
            $this->arResult['PHOTOS'][$key]['IS_NEW'] = true;
            $this->arResult['PHOTOS'][$key]['NEW_ID'] = $update['ID'];
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

        if(!Loader::IncludeModule("iblock"))
            return;

        CJSCore::init(["sidepanel", 'ui.buttons', 'bear.file.input', 'cool.editor']);


        $this->checkPermission();

        $this->defineAlbumData();

        $this->checkUpdates();

        $this->arResult['GRID_ID'] = 'rs_gallery_album';

        $this->IncludeComponentTemplate();
    }

}
