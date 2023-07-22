<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$APPLICATION->setTitle('Объявление');
global $USER;
$userId = $USER->getId();
?>

<div class="detal-element position-relative">
    <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $arResult['AD']['CREATED_BY'] == $userId)):?>
        <div cool-edit-here>
            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$arResult['AD']['EDIT_LINK']?>"></div>
            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arResult['AD']['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\AdItemTable')"></div>
        </div>
    <?endif;?>

    <div class="row">
        <?if($arResult['AD']['PREVIEW']['SRC']):?>
            <div class="col-6">
                <div class="preview">
                    <img src="<?=$arResult['AD']['PREVIEW']['SRC']?>">
                </div>
            </div>
        <?endif;?>
        <div class="col-6">
            <div class="primary-info">
                <div class="date"><?=$arResult['AD']['DATE']? date('d.m.Y', strtotime($arResult['AD']['DATE'])) : ''?></div>
                <div class="title"><?=$arResult['AD']['TITLE']?></div>
                <ul class="file-list">
                    <?foreach ($arResult['AD']['FILES'] as $file):?>
                        <li><a target="_blank" href="<?=$file['SRC']?>"><?=$file['ORIGINAL_NAME']?></a></li>
                    <?endforeach;?>
                </ul>
            </div>
        </div>
    </div>


    <div class="detail-text">
        <?=$arResult['AD']['DESCRIPTION']?>
    </div>
</div>

<script>
    new CoolEditor({
        component : 'rs:ad.union.list.item',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>