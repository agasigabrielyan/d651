<?php
 global $USER;
 $userId = $USER->getId();
?>

<div class="ads-container <?=$arResult['GRID_PARAMS']['ID']?> position-relative pt-5">

    <div cool-edit-here>
        <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['CREATE_AD_PATH']?>"></div>
        <?endif;?>
        <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="settings" data-link="<?=$arResult['SETTINGS_PATH']?>"></div>
        <?endif;?>
    </div>

    <div class="poisk-container ml-auto d-flex mb-3">

        <div class="search-icon">
            <svg width="30px" onclick="findAds(this.closest('.poisk-container').querySelector('input'))" class="svg-icon search-icon" aria-labelledby="title desc" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.9 19.7"><title id="title"></title><g class="search-path" fill="none" stroke="#848F91"><path stroke-linecap="square" d="M18.5 18.3l-5.4-5.4"></path><circle cx="8" cy="8" r="7"></circle></g></svg>
        </div>
        <input type="text" value="<?=$arParams['ad_like']?>" data-entity="iblock" onchange="findAds(this);" class="finder">
    </div>


    <div class="ads-list">
        <?foreach ($arResult['LIST'] as $ad):?>

            <div class="ad" data-new-id="<?=$ad['NEW_ID']?>" data-isnew="<?=$ad['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">

                <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $ad['CREATED_BY'] == $userId)):?>
                    <div cool-edit-here>
                        <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$ad['EDIT_LINK']?>"></div>
                        <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$ad['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\AdItemTable')"></div>
                    </div>
                <?endif;?>

                <?if($ad['PREVIEW']['ID']):?>
                    <img src="<?=CFile::ResizeImageGet($ad['PREVIEW']['ID'], ["width" => 1920 , "height" => 1080], BX_RESIZE_IMAGE_EXACT , true)['src']?>" class="preview">
                <?endif;?>
                <div class="content">
                    <div onclick="openSidePanel('<?=$ad['LINK']?>', 1000)" class="title"><?=$ad['TITLE']?></div>
                    <div class="description"><?=$ad['DESCRIPTION']?></div>
                </div>

                <div class="foot">
                    <?=$ad['DATE'] ? $ad['DATE']->format('d.m.Y') : ''?>
                </div>

            </div>

        <?endforeach;?>

        <?if(!$arResult['LIST']):?>
            <div class="empty-informer">
                <div class="empty-image"></div>
                <div class="empty-text">Нет данных</div>
            </div>
        <?endif;?>
    </div>

</div>

<script>
    window.ads_editor = new CoolEditor({
        component : 'rs:ad.union.list',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>