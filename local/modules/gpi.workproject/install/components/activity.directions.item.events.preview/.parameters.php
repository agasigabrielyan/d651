<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	"PARAMETERS" => array(
		"VARIABLE_ALIASES" => Array(
		),
		"SEF_MODE" => Array(
			"projects" => array(
				"NAME" => 'Проекты',
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"project" => array(
				"NAME" => 'Проект',
				"DEFAULT" => "projects/#project_id#/",
				"VARIABLES" => array('#project_id#/'),
			),
		),
		"AJAX_MODE" => array(),

	),
);

CIBlockParameters::AddPagerSettings(
	$arComponentParameters,
	GetMessage("T_IBLOCK_DESC_PAGER_NEWS"), //$pager_title
	true, //$bDescNumbering
	true, //$bShowAllParam
	true, //$bBaseLink
	$arCurrentValues["PAGER_BASE_LINK_ENABLE"]==="Y" //$bBaseLinkEnabled
);

CIBlockParameters::Add404Settings($arComponentParameters, $arCurrentValues);
?>
