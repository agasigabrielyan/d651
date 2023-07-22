<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<div class="<?=$arResult['GRID_ID']?> item direction-importants pt-5" id>

    <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
        <div cool-edit-here >
            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['AD_LINK']?>"></div>
        </div>
    <?endif;?>

    <div class="poisk-container ml-auto d-flex mt-3 mb-3">

        <div class="search-icon">
            <svg width="30px" onclick="findImportants(this.closest('.poisk-container').querySelector('input'))" class="svg-icon search-icon" aria-labelledby="title desc" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.9 19.7"><title id="title"></title><g class="search-path" fill="none" stroke="#848F91"><path stroke-linecap="square" d="M18.5 18.3l-5.4-5.4"></path><circle cx="8" cy="8" r="7"></circle></g></svg>
        </div>
        <input type="text" value="<?=$arParams['importants_like']?>" data-entity="iblock" onchange="findImportants(this);" class="finder">
    </div>

    <div class="importants-list row">
        <?foreach ($arResult['IMPORTANTS'] as $ad):?>
            <div class="important" data-new-id="<?=$ad['NEW_ID']?>" data-isnew="<?=$ad['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">

                <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>

                    <div cool-edit-here >
                        <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$ad['EDIT_LINK']?>"></div>
                        <div cool-edit-btn hidden data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$ad['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\DetailDirectionImportantTable')"></div>
                    </div>

                <?endif;?>

                <div class="content">
                    <div onclick="openSidePanel('<?=$ad['LINK']?>', 800)" class="title"><?=$ad['TITLE']?></div>
                    <div class="description"><?=$ad['DESCRIPTION']?></div>
                </div>

                <div class="foot">
                    `<div class='direction_name'><?= $ad['MAIN_DIRECTION_NAME']?></div>
                </div>
            </div>
        <?endforeach;?>
    </div>

</div>

<script>
    window.importants_editor = new CoolEditor({
        component : 'rs:activity.directions.item.importants',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>