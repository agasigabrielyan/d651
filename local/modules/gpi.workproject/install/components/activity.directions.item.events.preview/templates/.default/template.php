<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>



<div class="<?=$arResult['GRID_ID']?> item rupor-block">
    <div class="item-title position-relative">
        Объявления

        <div cool-edit-here >
            <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
                <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['AD_LINK']?>"></div>
            <?endif;?>
            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="readMore" data-script="location.href='<?=$arResult['LIST_LINK']?>';"></div>
        </div>

    </div>

    <div class="ads-list" style="margin-top: 16px;">
        <?foreach($arResult["EVENTS"] as $arItem):?>

            <div  class="ad editor-container"  data-new-id="<?=$arItem['NEW_ID']?>" data-isnew="<?=$arItem['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">

                <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>

                    <div cool-edit-here >
                        <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$arItem['EDIT_LINK']?>"></div>
                        <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arItem['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\DetailDirectionEventTable')"></div>
                    </div>

                <?endif;?>

                <img hidden src="<?=CFile::ResizeImageGet($arItem['FILE'], ["width" => 1920 , "height" => 1080], BX_RESIZE_IMAGE_EXACT , true)['src']?>" class="preview">

                <div class="content">
                    <div onclick="openSidePanel('<?=$arItem['LINK']?>/', 1000)" class="title"><?=$arItem['TITLE']?></div>
                    <div class="description"><?=$arItem['DESCRIPTION']?></div>
                </div>

                <div class="foot"><?= $arItem['DATE']? date('d.m.Y', strtotime($arItem['DATE'])) : ''?></div>
            </div>
        <?endforeach;?>
    </div>
</div>


<script>
    new CoolEditor({
        component : 'rs:activity.directions.item.events.preview',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>
