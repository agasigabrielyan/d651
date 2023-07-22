<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => 'Настройщик',
    "ICON" => "/images/news_all.gif",
    "COMPLEX" => "N",
    "PATH" => array(
        "ID" => "content",
        "CHILD" => array(
            "ID" => "rs_drive",
            "NAME" => 'Диск',
            "SORT" => 10,
            "CHILD" => array(
                "ID" => "rs_drive",
            ),
        ),
    ),
);
?>