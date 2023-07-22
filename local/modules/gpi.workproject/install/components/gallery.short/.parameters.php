<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Crm\Service\Container;

if(!Loader::IncludeModule("iblock") && !Loader::IncludeModule("highloadblock") && !Loader::IncludeModule("crm"))
    return;

if($arCurrentValues["ENTITY_TYPE"]){

    switch ($arCurrentValues["ENTITY_TYPE"])
    {
        case 'IBLOCK_ELEMENT':
            $entityList = devGetIblockList();
            break;

        case 'IBLOCK_SECTION':
            $entityList = devGetIblockList();
            break;

        case 'SMART':
            $entityList = devGetSmartList();
            break;

        case 'HIGHLOAD':
            $entityList = devGetHighloadList();
            break;
    }
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "ENTITY_TYPE" => array(
            "NAME" => 'Тип сущности',
            "TYPE" => "LIST",
            "VALUES" => [
                'IBLOCK' => 'Инфоблок',
                'IBLOCK_ELEMENT' => 'Элемент инфоблока',
                'IBLOCK_SECTION' => 'Раздел инфоблока',
                'SMART' => 'Смарт-процесс',
                'HIGHLOAD' => 'Хайлоад',
            ],
            'REFRESH' => 'Y'
        ),
        'ENTITY_TYPE_ID' => array(
            "NAME" => 'ID сущности',
            "TYPE" => "LIST",
            "VALUES" => $entityList,
            'REFRESH' => 'Y'
        ),
        'ENTITY_ID' => array(
            "NAME" => 'ID элемента сущности',
            "TYPE" => "STRING",
        ),
        "ENTITY_TYPE_EXTERNAL" => array(
            "NAME" => 'Статический тип сущности(Заменяет предыдущие три поля)',
            "TYPE" => "LIST",
            'REFRESH' => 'Y'
        ),
    ),
);




function devGetIblockList(){

    Loader::IncludeModule("iblock");
    $list = [];

    $res = CIBlock::GetList(
        Array(),
        Array(
        ), true
    );
    while($ar_res = $res->Fetch())
    {
        $id = $ar_res['ID'];
        $title = $ar_res['NAME'];
        $list[$id] = "[$id] $title";
    }
    return $list;

}


function devGetHighloadList(){
    Loader::IncludeModule("highloadblock");
    $list = [];
    foreach (\Bitrix\Highloadblock\HighloadBlockTable::getList()->fetchAll() as $hld){
        $id = $hld['ID'];
        $title = $hld['NAME'];
        $list[$id] = "[$id] $title";
    }
    return $list;
}

function devGetSmartList(){
    Loader::IncludeModule("crm");

    $typesMap = Container::getInstance()->getDynamicTypesMap();

    $typesMap->load([
        'isLoadStages' => false,
        'isLoadCategories' => false,
    ]);

    $list = [];

    foreach ($typesMap->getTypes() as $type)
    {
        $id = $type->getEntityTypeId();
        $title = $type->getTitle();
        $list[$id] = "[$id] $title";
    }
    return $list;
}
