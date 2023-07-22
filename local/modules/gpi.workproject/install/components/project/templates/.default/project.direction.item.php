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

<?php

$usersRS = Bitrix\Main\UserTable::getList([
    'filter' => ['ACTIVE' => 'Y'],
    'select' => ['FULL_NAME', 'ID'],
    'runtime' => [
        new \Bitrix\Main\Entity\ExpressionField(
            'AUTHOR_LAST_NAME',
            'COALESCE(%s, " ")',
            'LAST_NAME'
        ),
        new \Bitrix\Main\Entity\ExpressionField(
            'AUTHOR_NAME',
            'COALESCE(%s, " ")',
            'NAME'
        ),
        new \Bitrix\Main\Entity\ExpressionField(
            'AUTHOR_SECOND_NAME',
            'COALESCE(%s, " ")',
            'SECOND_NAME'
        ),
        new \Bitrix\Main\Entity\ExpressionField(
            'FULL_NAME',
            'CONCAT(%s, " ", %s, " ", %s)',
            ['AUTHOR_LAST_NAME','AUTHOR_NAME', 'AUTHOR_SECOND_NAME']
        ),
    ]
]);
while($user = $usersRS->fetch()){
    $users[] = [
        'id' => $user['ID'],
        'entityId' => 'user',
        'title' => $user['FULL_NAME'],
        'tabs' => ['US_LIST'],
    ];
}

$APPLICATION->IncludeComponent(
    "bitrix:ui.sidepanel.wrapper",
    "",
    [
        'POPUP_COMPONENT_NAME' => "rs:entity.edit",
        'CLOSE_AFTER_SAVE' => true,
        'POPUP_COMPONENT_PARAMS' => [
            'TABLE' => 'Gpi\Workproject\Orm\ProjectDirectionTable',
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
                    'TITLE' => 'Цель',
                    'CODE' => 'TARGET',
                    'TYPE' => 'string',
                    'REQUIRED' => true,
                ],
                [
                    'CODE' => 'PROJECT_ID',
                    'TYPE' => 'hidden',
                    'REQUIRED' => true,
                    'VALUE' => $arResult['VARIABLES']['project_id']
                ],
                [
                    'TITLE' => 'Руководитель',
                    'CODE' => 'DIRECTOR_ID',
                    'TYPE' => 'user',
                    'REQUIRED' => true,
                    'LIST' => $users
                ],
            ],
            'ID' => $arResult['VARIABLES']['direction_id'],
        ],
    ]
); ?>
