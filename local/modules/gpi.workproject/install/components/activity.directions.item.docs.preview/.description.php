<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Комплекасный компонент(gaz)',
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "content",
		"CHILD" => array(
			"ID" => "directions",
			"NAME" => 'Детальные направления деятельности',
			"SORT" => 10,
			"CHILD" => array(
				"ID" => "directions_cmpx",
			),
		),
	),
);

?>