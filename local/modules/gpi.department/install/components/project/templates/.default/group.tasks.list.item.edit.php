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
$request = Bitrix\Main\Context::getCurrent()->getRequest();
use Gpi\Workproject\Entity\UrlManager;
?>

<?$APPLICATION->IncludeComponent(
    "bitrix:ui.sidepanel.wrapper",
    "",
    [
        'POPUP_COMPONENT_NAME' => 'rs:tasks',
        'POPUP_COMPONENT_PARAMS' => array_merge( $arParams ?? [], $arResult ?? [], ['SEF_FOLDER' => UrlManager::getGroupTasksLink($arResult['VARIABLES']['project_id'], $arResult['VARIABLES']['group_id'])]),
    ]
);?>
