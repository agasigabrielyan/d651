<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => 'Комплексный компонент',
	"ICON" => "/images/icon.gif",
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