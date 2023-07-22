<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->setTitle('Важно');
$important = $arResult['IMPORTANT'];
?>
<div class="detal-element <?=$arResult['GRID_ID']?>" data-new-id="<?=$important['NEW_ID']?>" data-isnew="<?=$important['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">

    <?if(array_intersect(['X', 'W'], $arParams['USER_PERMISSIONS'])):?>

        <div cool-edit-here >
            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$important['EDIT_LINK']?>"></div>
            <div cool-edit-btn hidden data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$important['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\DetailDirectionImportantTable')"></div>
        </div>

    <?endif;?>
    
    <div class="row">

        <div class="col-12">
            <div class="primary-info">
                <div class="title"><?=$important['TITLE']?></div>
            </div>
        </div>
    </div>

    <div class="detail-text">
        <?=$important['DESCRIPTION']?>
    </div>
</div>

<script>
    new CoolEditor({
        component : 'rs:activity.directions.item.importants.item',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>