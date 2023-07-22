<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
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
?>

<div class="direction-text" style="font-family: system-ui">
	<b class="direction-label">Детальное направление:</b> <a  onclick="" href="<?=$arParams['DIRECTION_LINK']?>" class="direction-link"><?=$arParams['DIRECTION_DATA']['TITLE']?></a>
</div>

<?
$APPLICATION->IncludeComponent(
	"bitrix:ui.sidepanel.wrapper",
	"",
	[
		'POPUP_COMPONENT_NAME' => "rs:ad.union",
		'CLOSE_AFTER_SAVE' => true,
		'POPUP_COMPONENT_PARAMS' => [
			'SEF_FOLDER' => $arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#'],[$arResult['VARIABLES']['main_id'], $arResult['VARIABLES']['direction_id']],$arResult['URL_TEMPLATES']['detail_direction_item_ads']),
			'UNION_ID' => $arParams['DIRECTION_DATA']['AD_UNION_ID'],
		],
	]
); ?>

<style>
    .middleColumn.other {
        background: transparent;
    }
</style>
