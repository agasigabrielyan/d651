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
        'POPUP_COMPONENT_NAME' => "rs:entity.edit",
        'CLOSE_AFTER_SAVE' => true,
        'POPUP_COMPONENT_PARAMS' => [
            'TABLE' => 'Gpi\Workproject\Orm\ForumDiscussionTable',
            'MODULS' => [
                'gpi.workproject'
            ],
            'COLUMNS' => [
                [
                    'TITLE' => 'Название',
                    'CODE' => 'TITLE',
                    'TYPE' => 'string',
                    'REQUIRED' => true,
                ],
                [
                    'TITLE' => 'Описание',
                    'CODE' => 'DESCRIPRION',
                    'TYPE' => 'html',
                    'REQUIRED' => true,
                ],
                [
                    'TITLE' => 'Файлы',
                    'CODE' => 'FILES',
                    'TYPE' => 'file',
                    'MULTIPLE' => 'Y',
                    'REQUIRED' => true,
                ],
                [
                    'VALUE' => $arParams['FORUM_ID'],
                    'CODE' => 'FORUM_ID',
                    'TYPE' => 'hidden',
                ],
            ],
            'ID' => $arResult['VARIABLES']['discussion_id'],
        ],
    ]
); ?>
