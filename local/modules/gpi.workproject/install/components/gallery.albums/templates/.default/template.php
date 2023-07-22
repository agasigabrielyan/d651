<?php
global $USER;
$userId = $USER->getId();
?>

<div class="albums-list position-relative">

    <div cool-edit-here>
        <?if(array_intersect(['W', 'X'], $arParams['USER_PERMISSIONS'])):?>
            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="add" data-script="createAlbum();"></div>
        <?endif;?>
        <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="settings" data-link="<?=$arParams['SEF_FOLDER']?>settings/"></div>
        <?endif;?>
    </div>

    <?foreach ($arResult['ALBUMS'] as $album):?>
        
        <div class="album" data-new-id="<?=$album['NEW_ID']?>" data-isnew="<?=$album['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">
            <img src="<?=$templateFolder?>/images/fly.jpg">
            <a class="container-link" onclick="" href='<?=$album['ALBUM_LINK']?>'>

                <div class="caption" >
                    <?=$album['TITLE']?>
                </div>

                <div class="anons-picture-container">

                    <div class="anons-border"></div>
                    <div class="anons-picture" style="background-image: url(<?=$album['PREVIEW_LINK']?>)"></div>
                </div>

                <div class="read-album">Смотреть альбом ></div>
            </a>


            <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $album['CREATED_BY'] == $userId)):?>
                <div cool-edit-here>
                    <div cool-edit-btn data-action-reload="true" data-type="script" data-action="edit" data-script='editAlbum(<?=\Bitrix\Main\Web\Json::encode($album)?>)'></div>
                    <div cool-edit-btn data-action-reload="true" data-type="link" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$album['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\GalleryAlbumTable')"></div>
                </div>
            <?endif;?>

        </div>
    <?endforeach;?>

</div>

<script>
    window.albumsEditor = new CoolEditor({
        component : '<?=explode('/', $templateFolder)[3]?>:<?=explode('/', $templateFolder)[4]?>',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>