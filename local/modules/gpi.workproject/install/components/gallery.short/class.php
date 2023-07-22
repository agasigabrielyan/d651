<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use \Bitrix\Main\Loader,
    \Bitrix\Main\Application,
    Bitrix\Main\Engine\Contract\Controllerable,
    Gpi\Workproject\Orm,
    Gpi\Workproject\Helper,
    Gpi\Workproject\Entity;



class RSGalleryShort extends  \CBitrixComponent implements Controllerable{

    function configureActions(){}

    function definePhotos(){
        if(!Loader::includeModule("gpi.workproject"))
            return;

        $alowedGalleryIds = Entity\EditorManager::getAlowedGalleryIds();


        if(!$alowedGalleryIds)
            return;

        $this->arResult['PHOTOS'] = Orm\GalleryAlbumItemTable::getList([
            'filter' => ['ALBUM.GALLERY_ID' => $alowedGalleryIds],
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
                ),
                'ALBUM' => [
                    'data_type' => 'Gpi\Workproject\Orm\GalleryAlbumTable',
                    'reference' => [
                        'this.ALBUM_ID' => 'ref.ID',
                    ]
                ]
            ]
        ])->fetchAll();

    }

    public function executeComponent() {

        if(!Loader::IncludeModule("gpi.workproject"))
            return;


        $this->definePhotos();

        $this->arResult['GRID_ID'] = 'rs_gallery_short';

        $this->IncludeComponentTemplate();
    }

}
