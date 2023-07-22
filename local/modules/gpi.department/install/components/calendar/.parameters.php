<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	"PARAMETERS" => array(
		"VARIABLE_ALIASES" => Array(
		),
		"SEF_MODE" => Array(
			"calendar" => array(
				"NAME" => 'Календарь',
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"event" => array(
				"NAME" => 'Проект',
				"DEFAULT" => "events/#event_id#/",
				"VARIABLES" => array('#event_id#'),
			),
		),
		"AJAX_MODE" => array(),
        'CALENDAR_ID' => array(
            "NAME" => 'ID календаря',
            "TYPE" => "NUMBER",
        ),
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
