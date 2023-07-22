<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Crm\Service\Container;

if(!Loader::IncludeModule("iblock") && !Loader::IncludeModule("highloadblock"))
    return;

$arComponentParameters = array(
    "PARAMETERS" => array(

    ),
);
