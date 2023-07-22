<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => 'Инструмент настройки прав доступа пользователей сущности',
    "COMPLEX" => "Y",
    "PATH" => array(
        "ID" => "content",
        "CHILD" => array(
            "ID" => "workprojects",
            "NAME" => 'Совместная работа',
            "SORT" => 10,
            "CHILD" => array(
                "ID" => "workprojects_cmpx",
            ),
        ),
    ),
);

?>