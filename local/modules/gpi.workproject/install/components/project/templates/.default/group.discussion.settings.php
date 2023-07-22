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
        'POPUP_COMPONENT_NAME' => 'rs:forum.discussion',
        'CLOSE_AFTER_SAVE' => true,
        'POPUP_COMPONENT_PARAMS' => [
            'DISCUSSION_ID' => $arResult['VARIABLES']['theme_id'],
            'FORUM_ID' => $arResult['GROUP']['FORUM_ID'],
            'SEF_FOLDER' => UrlManager::getGroupDiscussionListLink($arResult['VARIABLES']['project_id'], $arResult['VARIABLES']['group_id']),
            'PROJECT_GROUP_EXISTS' => $arParams['PROJECT_GROUP_EXISTS'],
        ],
    ]
);?>
