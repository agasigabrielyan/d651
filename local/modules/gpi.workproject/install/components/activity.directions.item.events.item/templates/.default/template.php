<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->setTitle('Объявление');
?>


<div class="<?=$arResult['GRID_ID']?> detal-element position-relative" data-new-id="<?=$arResult['EVENT']['NEW_ID']?>" data-isnew="<?=$arResult['EVENT']['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">

    <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>
        <div cool-edit-here >
            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$arResult['EVENT']['EDIT_LINK']?>"></div>
            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arResult['EVENT']['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\DetailDirectionEventTable')"></div>
        </div>
    <?endif;?>

    <div class="row">
        <?if($arResult['EVENT']['PREVIEW_PICTURE_PATH']):?>
            <div hidden class="col-6">
                <div class="preview">
                    <img src="<?=$arResult['EVENT']['PREVIEW_PICTURE_PATH']?>">
                </div>
            </div>
        <?endif;?>
        <div class="col-12">
            <div class="primary-info">
                <div class="date"><?=$arResult['EVENT']['DATE']? date('d.m.Y', strtotime($arResult['EVENT']['DATE'])) : ''?></div>
                <div class="title"><?=$arResult['EVENT']['TITLE']?></div>
            </div>
        </div>
    </div>

    <div class="detail-text">
        <?=$arResult['EVENT']['DESCRIPTION']?>
    </div>
</div>

<script>
    new CoolEditor({
        component : 'rs:activity.directions.item.events.item',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>
