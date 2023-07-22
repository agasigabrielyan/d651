<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => 'Альбом',
    "ICON" => "/images/news_all.gif",
    "COMPLEX" => "N",
    "PATH" => array(
        "ID" => "content",
        "CHILD" => array(
            "ID" => "gallery",
            "NAME" => 'Галерея',
            "SORT" => 10,
            "CHILD" => array(
                "ID" => "gallery",
            ),
        ),
    ),
);
?>