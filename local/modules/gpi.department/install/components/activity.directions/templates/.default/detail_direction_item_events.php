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

<?
$APPLICATION->IncludeComponent(
    "rs:activity.directions.item.events",
    "",
    array_merge($arParams, $arResult),
    $component,
    ["HIDE_ICONS"=>"Y"]
);?>
<style>
    .middleColumn.other {
        background: transparent;
    }
</style>