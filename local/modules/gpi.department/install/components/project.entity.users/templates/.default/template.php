<?

$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$grid_options = new Bitrix\Main\Grid\Options($arResult['GRID_PARAMS']['ID']);
$sort = $grid_options->GetSorting();
$nav_params = $grid_options->GetNavParams();
$nav = new Bitrix\Main\UI\PageNavigation($arResult['GRID_PARAMS']['ID']);
?>
<script>
    window.usersList = <?=json_encode($arResult['USERS'])?>;
    window.constsCategories = <?=json_encode(array_values($arResult['CATEGORIES_CONSTS']))?>;
    window.project_id = <?=$arParams['VARIABLES']['project_id']?>;
    window.grid_location = location.href;
</script>
<?php

$nav->allowAllRecords(false)
    ->setPageSize($arResult['GRID_PARAMS']['PAGE_SIZE'])
    ->setRecordCount($arResult['GRID_PARAMS']['ITEMS_TOTAL'])
    ->setCurrentPage($arResult['GRID_PARAMS']['CURRENT_PAGE'])
    ->initFromUri();


$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
    'FILTER_ID' => $arResult['GRID_PARAMS']['ID'],
    'FILTER' => array_map(function($column){

        if($column['type'] == 'multiselect')
            $column['type'] = 'list';
        
        return $column;
    },array_values($arResult['COLUMNS'])),
    'ENABLE_LIVE_SEARCH' => true,
    'ENABLE_LABEL' => true
],
    false,
    array("HIDE_ICONS" => false)
);

?>
<?php


if ($request->isAjaxRequest() && ($request['GRID_ID'] == $arResult['GRID_PARAMS']['ID'] || $request['params']['FILTER_ID'] == $arResult['GRID_PARAMS']['ID'])) {
    $APPLICATION->RestartBuffer();
    ob_start();
}

$gridParams = [
    'GRID_ID' => $arResult['GRID_PARAMS']['ID'],
    'COLUMNS' => array_values($arResult['COLUMNS']),
    'ROWS' =>  $arResult['ROWS'],
    'SHOW_ROW_CHECKBOXES' => false,
    'NAV_OBJECT' => $nav,
    'PAGE_SIZES' => [
        ['NAME' => "5", 'VALUE' => '5'],
        ['NAME' => '10', 'VALUE' => '10'],
        ['NAME' => '20', 'VALUE' => '20'],
        ['NAME' => '50', 'VALUE' => '50'],
        ['NAME' => '100', 'VALUE' => '100']
    ],
    'AJAX_OPTION_JUMP'          => 'Y',
    'AJAX_MODE'                 => 'N',
    'AJAX_OPTION_HISTORY'       => 'N',
    'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
    'SHOW_CHECK_ALL_CHECKBOXES' => false,
    'SHOW_ROW_ACTIONS_MENU'     => true,
    'SHOW_GRID_SETTINGS_MENU'   => true,
    'SHOW_NAVIGATION_PANEL'     => true,
    'SHOW_PAGINATION'           => true,
    'SHOW_SELECTED_COUNTER'     => true,
    'SHOW_TOTAL_COUNTER'        => true,
    'SHOW_PAGESIZE'             => true,
    'ALLOW_COLUMNS_SORT'        => true    ,
    'ALLOW_COLUMNS_RESIZE'      => true,
    'ALLOW_HORIZONTAL_SCROLL'   => true,
    'ALLOW_SORT'                => true,
    'ALLOW_PIN_HEADER'          => true,
    'CURRENT_PAGE' => $nav->getCurrentPage(),
    'TOTAL_ROWS_COUNT' => $arResult['GRID_PARAMS']['ITEMS_TOTAL'],
    'ACTION_PANEL' => $arResult['GRID_PARAMS']['ACTION_PANEL'],
    'EDITABLE' => true,
    'ALLOW_INLINE_EDIT' => true,
    'DATA_FOR_EDIT' => [
        ''
    ],
    'ENABLE_FIELDS_SEARCH' => 'Y'

];

if($arResult['GRID_PARAMS']['ACTION_PANEL']){
    $gridParams['SHOW_CHECK_ALL_CHECKBOXES'] = true;
    $gridParams['SHOW_ROW_CHECKBOXES'] = true;
}

$APPLICATION->IncludeComponent(
    'bitrix:main.ui.grid',
    '',
    $gridParams
);

if($request->isAjaxRequest() && ($request['GRID_ID'] == $arResult['GRID_PARAMS']['ID'] || $request['params']['FILTER_ID'] == $arResult['GRID_PARAMS']['ID'])){
    die();
}