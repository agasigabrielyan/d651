<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => 'Настройщик',
    "ICON" => "/images/news_all.gif",
    "COMPLEX" => "N",
    "PATH" => array(
        "ID" => "content",
        "CHILD" => array(
            "ID" => "forum.discussion",
            "NAME" => 'Форум'
        )
    ),
);
?>