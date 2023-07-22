<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$rsUserFields= \Bitrix\Main\UserFieldTable::Getlist(['filter'=>['ENTITY_ID' => 'USER']]);

while($field = $rsUserFields->fetch()){
	$UsersFileds[$field['FIELD_NAME']]=$field['FIELD_NAME'];
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"IMG" => array(
			"NAME" => GetMessage("IMG"),
			"TYPE" => "LIST",
			"VALUES" => $arProperty,
		),
		"DEV_PARAM" => array(
			"NAME" => GetMessage("DEV_PARAM"),
			"TYPE" => "LIST",
			"VALUES" => $arProperty,
		),
		"IMG_LINK" => array(
			"NAME" => GetMessage("IMG_LINK"),
			"TYPE" => "STRING",
			"DEFAULT" => '/cpgp/img/service/',
		),
		"LINK" => array(
			"NAME" => GetMessage("LINK"),
			"TYPE" => "LIST",
			"VALUES" => $arProperty,
		),
		"TITLE_TEXT" => array(
			"NAME" => GetMessage("TITLE_TEXT"),
			"TYPE" => "STRING",
			"DEFAULT" => 'Цифровые сервисы',
		),
		"FAVOR_DIGIT_LINK_FIELD" => array(
			"NAME" => GetMessage("FAVOR_DIGIT_LINK_FIELD"),
			"TYPE" => "LIST",
			"VALUES" => $UsersFileds,
		),
	),
);

return $arParams;
?>