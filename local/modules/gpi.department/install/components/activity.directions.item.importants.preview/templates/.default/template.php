<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>


<div class="<?=$arResult['GRID_ID']?> item direction-importants">

    <div class="item-title position-relative">Важно

        <?if(count($arResult["IMPORTANTS"]) == 0 && array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
            <div cool-edit-here >
                <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['AD_LINK']?>"></div>
            </div>
        <?endif?>

    </div>




    <div class="importants-list" style="margin-top: 16px;">
            <?foreach($arResult["IMPORTANTS"] as $arItem):?>


                <div class="item banner position-relative" data-new-id="<?=$arItem['NEW_ID']?>" data-isnew="<?=$arItem['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">

                    <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>

                        <div cool-edit-here >
                            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$arItem['EDIT_LINK']?>"></div>
                            <div cool-edit-btn hidden data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arItem['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\DetailDirectionImportantTable')"></div>
                        </div>

                    <?endif;?>

                    <div class="background-preview" style="<?=$arItem['PREVIEW_PICTURE_PATH']? "background-image:url({$arItem['PREVIEW_PICTURE_PATH']});" : ''?>" >
                        <div class="preview-container">
                            <div class='anons'><?=$arItem['TITLE']?></div>
                            <div class='text'> <?=$arItem['DESCRIPTION']?></div>
                        </div>
                    </div>
                </div>

            <?endforeach;?>

    </div>

</div>

<script>
    new CoolEditor({
        component : 'rs:activity.directions.item.importants.preview',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>
