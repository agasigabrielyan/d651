<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => 'Список',
    "ICON" => "/images/news_all.gif",
    "COMPLEX" => "N",
    "PATH" => array(
        "ID" => "content",
        "CHILD" => array(
            "ID" => "ads.union",
            "NAME" => 'Объединение объявлений'
        )
    ),
);
?>