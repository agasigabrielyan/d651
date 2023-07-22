<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	"PARAMETERS" => array(
		"VARIABLE_ALIASES" => Array(
		),
		"SEF_MODE" => Array(
			"list" => array(
				"NAME" => 'Объявления',
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"item" => array(
				"NAME" => 'Объявление',
				"DEFAULT" => "#ad_id#/",
				"VARIABLES" => array('#ad_id#'),
			),
		),
		"AJAX_MODE" => array(),
        'UNION_ID' => array(
            "NAME" => 'ID объединения',
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
