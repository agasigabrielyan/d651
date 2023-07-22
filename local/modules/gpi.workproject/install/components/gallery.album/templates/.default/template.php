<?php
global $USER;
$userId = $USER->getId();
?>

<script>
    window.rs_album_grid_id = '<?=$arResult['GRID_ID']?>';
    window.album_id = '<?=$arParams['VARIABLES']['album_id']?>';
</script>


<div class="album position-relative">

    <div cool-edit-here>

        <div cool-edit-btn data-action-reload="true" data-type="script" data-action="prevLink" data-script="location.href='<?=$arParams['SEF_FOLDER']?>'"></div>
        <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="add" data-script="loadPhotos();"></div>
        <?endif;?>
    </div>

    <?foreach ($arResult['PHOTOS'] as $photo):?>
        <div class="photo" data-new-id="<?=$photo['NEW_ID']?>" data-isnew="<?=$photo['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">

            <?if(
                    array_intersect(['X'], $arParams['USER_PERMISSIONS'])
                ||
                   (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $photo['CREATED_BY'] == $userId)

            ):?>

                <div cool-edit-here>
                    <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$photo['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\GalleryAlbumItemTable')"></div>
                </div>

            <?endif;?>
            <img data-fancybox="photo" href="<?=$photo['PICTURE_PATH']?>" data-caption="<?=$photo['TITLE']?>" class="gallery-lightbox" src="<?=CFile::ResizeImageGet($photo['FILE'], ["width" => 640 , "height" => 360], BX_RESIZE_IMAGE_EXACT , true)['src']?>" alt="">

        </div>
    <?endforeach;?>
</div>


<script>
    window.albumsItemEditor = new CoolEditor({
        component : '<?=explode('/', $templateFolder)[3]?>:<?=explode('/', $templateFolder)[4]?>',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>