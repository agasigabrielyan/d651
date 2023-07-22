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
            'TABLE' => 'Gpi\Workproject\Orm\DetailDirectionEventTable',
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
                    'CODE' => 'DESCRIPTION',
                    'TYPE' => 'html',
                    'REQUIRED' => true,
                ],
                [
                    'TITLE' => 'Дата',
                    'CODE' => 'DATE',
                    'TYPE' => 'date',
                    'REQUIRED' => true,
                ],
                /*[
                    'TITLE' => 'Файл',
                    'CODE' => 'FILE',
                    'TYPE' => 'file',
                    'REQUIRED' => true,
                ],*/
                [
                    'VALUE' => $arResult['VARIABLES']['direction_id'],
                    'CODE' => 'DETAIL_DIRECTION_ID',
                    'TYPE' => 'hidden'
                ],
            ],
            'ID' => $arResult['VARIABLES']['event_id'],
        ],
    ]
); ?>
