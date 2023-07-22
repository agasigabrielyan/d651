<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Элемент',
	"COMPLEX" => "Y",
    "PATH" => array(
        "ID" => "content",
        "CHILD" => array(
            "ID" => "ads.union",
            "NAME" => 'Объединение объявлений'
        )
    ),
);

?>