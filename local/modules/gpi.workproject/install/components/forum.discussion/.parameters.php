<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	"PARAMETERS" => array(
		"VARIABLE_ALIASES" => Array(
		),
		"SEF_MODE" => Array(
			"discussion.list" => array(
				"NAME" => 'Список обсуждений формума',
				"DEFAULT" => "",
				"VARIABLES" => array(),
			),
			"discussion.item" => array(
				"NAME" => 'Обсуждение',
				"DEFAULT" => "discussions/#discussion_id#/",
				"VARIABLES" => array('#discussion_id#'),
			),
		),
		"AJAX_MODE" => array(),
        'FORUM_ID' => array(
            "NAME" => 'ID форума',
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
