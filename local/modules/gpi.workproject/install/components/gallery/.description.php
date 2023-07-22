<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => 'Комплексный компонент',
	"COMPLEX" => "Y",
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