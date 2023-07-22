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
use Gpi\Workproject\Entity\UrlManager;

?>


<?
$APPLICATION->IncludeComponent(
    "bitrix:ui.sidepanel.wrapper",
    "",
    [
        'POPUP_COMPONENT_NAME' => 'rs:calendar',
        'CLOSE_AFTER_SAVE' => true,
        'POPUP_COMPONENT_PARAMS' => array_merge(
        [
            "SEF_FOLDER" => UrlManager::getProjectCalendarLink($arResult['PROJECT']['ID']),
            "CALENDAR_ID" => $arResult['PROJECT']['CALENDAR_ID'],
            'PROJECT_GROUP_EXISTS' => $arParams['PROJECT_GROUP_EXISTS'],
            'PROJECT_GROUP_EXISTS_GROUUP_ID' => $arParams['PROJECT_GROUP_EXISTS_GROUUP_ID'],
        ]),
    ]
);?>