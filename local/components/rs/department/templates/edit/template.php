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

<div class="dep-slider">
    <?php
    $APPLICATION->IncludeComponent(
        "bitrix:ui.sidepanel.wrapper",
        "",
        [
            'POPUP_COMPONENT_NAME' => "rs:editor",
            'CLOSE_AFTER_SAVE' => true,
            'POPUP_COMPONENT_PARAMS' => [
                'TABLE' => 'Gpi\Workproject\Orm\DepartmentTable',
                'MODULS' => [
                    'gpi.workproject'
                ],
                'COLUMNS' => [
                    [
                        'NAME' => 'Описание для анонса',
                        'CODE' => 'TITLE',
                        'TYPE' => 'text',
                        'REQUIRED' => true,
                    ],
                    [
                        'NAME' => 'Описание',
                        'CODE' => 'TARGET',
                        'TYPE' => 'text',
                        'REQUIRED' => true,
                    ],
                    [
                        'NAME' => 'Руководитель',
                        'CODE' => 'DIRECTOR_ID',
                        'TYPE' => 'user',
                        'REQUIRED' => true,
                    ],
                ],
                'ID' => $arResult['VARIABLES']['project_id'],
            ],
        ]
    ); ?>
</div>