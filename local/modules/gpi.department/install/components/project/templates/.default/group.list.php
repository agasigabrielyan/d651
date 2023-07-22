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
    "bitrix:ui.sidepanel.wrapper",
    "",
    [
        'POPUP_COMPONENT_NAME' => 'rs:group.list',
        'CLOSE_AFTER_SAVE' => true,
        'POPUP_COMPONENT_PARAMS' => array_merge([
            'UNION_ID' => $arResult['PROJECT']['GROUP_UNION_ID'],
            'PROJECT_ID' => $arResult['PROJECT']['ID'],
            'GROUP_ID' => $arResult['VARIABLES']['group_id']], $arParams, $arResult),
    ]
);?>