<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->setTitle('Объявления');
?>

<div class="<?=$arResult['GRID_ID']?>">
    <div class="direction-text" style="font-family: system-ui">
        <b class="direction-label">Детальное направление:</b> <a  onclick="" href="<?=$arParams['DIRECTION_LINK']?>" class="direction-link"><?=$arParams['DIRECTION_DATA']['TITLE']?></a>
    </div>

    <div class="poisk-container ml-auto d-flex mt-3 mb-3">

        <div class="search-icon">
            <svg width="30px" onclick="findEvents(this.closest('.poisk-container').querySelector('input'))" class="svg-icon search-icon" aria-labelledby="title desc" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.9 19.7"><title id="title"></title><g class="search-path" fill="none" stroke="#848F91"><path stroke-linecap="square" d="M18.5 18.3l-5.4-5.4"></path><circle cx="8" cy="8" r="7"></circle></g></svg>
        </div>
        <input type="text" value="<?=$arParams['events_like']?>" data-entity="iblock" onchange="findEvents(this);" class="finder">
    </div>

    <div class="item rupor-block">

        <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
            <div cool-edit-here>
                <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['AD_LINK']?>"></div>
            </div>
        <?endif;?>


        <div class="events-list row">
            <?foreach ($arResult['EVENTS'] as $ad):?>
                <div class="event" data-new-id="<?=$ad['NEW_ID']?>" data-isnew="<?=$ad['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">

                    <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
                        <div cool-edit-here >
                            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$ad['EDIT_LINK']?>"></div>
                            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$ad['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\DetailDirectionEventTable')"></div>
                        </div>
                    <?endif;?>

                    <img hidden src="<?=CFile::ResizeImageGet($ad['FILE'], ["width" => 1920 , "height" => 1080], BX_RESIZE_IMAGE_EXACT , true)['src']?>" class="preview">

                    <div class="content">
                        <div onclick="openSidePanel('<?=$ad['LINK']?>', 1000)" class="title"><?=$ad['TITLE']?></div>
                        <div class="description"><?=$ad['DESCRIPTION']?></div>
                    </div>

                    <div class="foot"><?= $ad['DATE']? date('d.m.Y', strtotime($ad['DATE'])) : ''?></div>
                </div>

            <?endforeach;?>
        </div>

    </div>
</div>

<script>
    window.events_editor = new CoolEditor({
        component : 'rs:activity.directions.item.events',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>
