<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult['DIRECTION] */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);


$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$APPLICATION->setTitle($arResult['DIRECTION']['TITLE']);
?>

<div class="activity_direction detail editor-container <?=$arResult['GRID_ID']?>">

    <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
        <div cool-edit-here>
            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$arResult['DIRECTION']['EDIT_LINK']?>"></div>
            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arResult['DIRECTION']['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\DetailDirectionTable')"></div>
        </div>
    <?endif;?>




    <div class="curators">
        <b>Направление деятельности:</b> <?=$arResult['DIRECTION']['MAIN_TITLE']?>
    </div>


    <div class="curators">
        <b>Куратор:</b> <?=implode(', ', $arResult['DIRECTION']['MAIN_CURATORS'])?>
    </div>

</div>

<script>
    new CoolEditor({
        component : 'rs:activity.directions.item',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>


<div class="activity_direction detail editor-container ">
    <div class="row">

        <div class="col-6">

            <?$APPLICATION->IncludeComponent(
                "rs:activity.directions.item.local.rulls.preview",
                '',
                $arParams,
                false
            );?>
        </div>


        <div class="col-6">

            <?$APPLICATION->IncludeComponent(
                "rs:activity.directions.item.docs.preview",
                '',
                $arParams,
                false
            );?>

        </div>

        <div class="col-4 prevent-height">


            <div class="item events direction-ads">
                <div class="item-title position-relative">
                    События
                    <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
                        <div cool-edit-here>
                            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#', '#ad_id#'],[$arParams['VARIABLES']['main_id'], $arParams['VARIABLES']['direction_id'], 0],$arParams['URL_TEMPLATES']['detail_direction_item_ads_item_edit'])?>"></div>
                            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="readMore" data-script="location.href='<?=$arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#'],[$arParams['VARIABLES']['main_id'], $arParams['VARIABLES']['direction_id']],$arParams['URL_TEMPLATES']['detail_direction_item_ads'])?>'"></div>
                        </div>
                    <?endif;?>

                </div>

                <?
                \CBitrixComponent::includeComponentClass('rs:ad.union');
                $comp = new ADUnion();
                $comp->executeComponent();

                $APPLICATION->IncludeComponent(
                    "rs:ad.union.list",
                    "",
                    array_merge($comp->arResult,[
                        'SEF_FOLDER' => $arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#'],[$arParams['VARIABLES']['main_id'], $arParams['VARIABLES']['direction_id']],$arParams['URL_TEMPLATES']['detail_direction_item_ads']),
                        'UNION_ID' => $arParams['DIRECTION_DATA']['AD_UNION_ID'],
                        'MAX_LIST_ITEMS' => 1,
                        'PAGE' => 'directions.special'
                    ]),
                    false,
                    ["HIDE_ICONS"=>"Y"]
                ); ?>

            </div>
        </div>

        <div class="col-4 prevent-height">

            <?$APPLICATION->IncludeComponent(
                "rs:activity.directions.item.events.preview",
                '',
                $arParams,
                false,
                ["HIDE_ICONS"=>"Y"]
            );?>
        </div>


        <div class="col-4 prevent-height">


            <?$APPLICATION->IncludeComponent(
                "rs:activity.directions.item.importants.preview",
                '',
                $arParams,
                false,
                ["HIDE_ICONS"=>"Y"]
            );?>
        </div>
    </div>
</div>