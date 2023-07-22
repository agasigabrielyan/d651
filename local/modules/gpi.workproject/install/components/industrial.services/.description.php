<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("COMPONENT_DESC"),
	"ICON" => "/images/photo_view.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 40,
	"PATH" => array(
		"ID" => "rs",
		"NAME" => GetMessage("COMPONENT_PATH_NAME"),
		"SORT" => 20,
	),
);

?>