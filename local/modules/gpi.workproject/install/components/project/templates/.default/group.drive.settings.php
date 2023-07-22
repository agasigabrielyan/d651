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
        'POPUP_COMPONENT_NAME' => 'rs:storage',
        'CLOSE_AFTER_SAVE' => true,
        'POPUP_COMPONENT_PARAMS' => [
            'STORAGE_ID' => $arResult['GROUP']['STORAGE_ID'],
            'SEF_FOLDER' => UrlManager::getGroupDriveLink($arResult['PROJECT']['ID'], $arResult['GROUP']['ID']),
            'PARENT_ID' => $arResult['VARIABLES']['folder_id'],
        ],
    ]
);?>
