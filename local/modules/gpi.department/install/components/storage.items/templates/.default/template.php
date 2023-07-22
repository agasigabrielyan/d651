<?

$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$grid_options = new Bitrix\Main\Grid\Options($arResult['GRID_PARAMS']['ID']);
$sort = $grid_options->GetSorting();
$nav_params = $grid_options->GetNavParams();
$nav = new Bitrix\Main\UI\PageNavigation($arResult['GRID_PARAMS']['ID']);

$nav->allowAllRecords(false)
    ->setPageSize($arResult['GRID_PARAMS']['PAGE_SIZE'])
    ->setRecordCount($arResult['GRID_PARAMS']['ITEMS_TOTAL'])
    ->setCurrentPage($arResult['GRID_PARAMS']['CURRENT_PAGE'])
    ->initFromUri();


if ($request->isAjaxRequest() && ($request['DEV_GRID_ID'] == $arResult['GRID_PARAMS']['ID'] || $request['GRID_ID'] == $arResult['GRID_PARAMS']['ID'] || $request['params']['FILTER_ID'] == $arResult['GRID_PARAMS']['ID'])) {
    $APPLICATION->RestartBuffer();
    ob_start();
}
?>
<div class="drive-container">

    <div class="brand-container">
        <div class="brandcamps">
            <?php foreach ($arResult['BRADCAMPS'] as $link):?>
                <?if($link['current']):?>
                    <div class="item"><?=$link['title']?></div>
                <?else:?>
                    <a class="item" onclick="window.SomeJsDriveClass.openFolder('<?=$link['link']?>')"><?=$link['title']?></a>
                <?endif;?>

            <?endforeach;?>
        </div>
    </div>

    <?
    $APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
        'FILTER_ID' => $arResult['GRID_PARAMS']['ID'],
        'FILTER' => array_values($arResult['COLUMNS']),
        'ENABLE_LIVE_SEARCH' => true,
        'ENABLE_LABEL' => true
    ],
        false,
        array("HIDE_ICONS" => false)
    );
    ?>


    <div class="grid-container">

        <script>
            window.driveGridId = '<?=$arResult['GRID_PARAMS']['ID']?>';
            window.drivePath = '<?=$arParams['CURRENT_FOLDER']?>';
            <?if($arParams['PARENT_ID']):?>
                window.parentId = <?=$arParams['PARENT_ID']?>;
            <?endif;?>
            window.storageId = <?=$arParams['STORAGE_ID']?>;
        </script>




            <span class="button-drive">
                <?if(array_intersect(['W', 'X'], $arParams['USER_PERMISSIONS'])):?>
                    <div style="width: 248px;" onclick="window.SomeJsDriveClass.createFolder();" class="ui-btn ui-btn-icon-add ui-btn-primary">Создать папку</div>
                    <div style="width: 248px;" onclick="window.SomeJsDriveClass.createFile();" class="ui-btn ui-btn-icon-add ui-btn-success">Загрузить файлы</div>
                <?endif;?>

                <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
                    <div class='ui-btn ui-btn-icon-setting' onclick="openSidePanel('<?=$arParams['SEF_FOLDER']?>settings/', 600)"></div>
                <?endif;?>
            </span>


        <?php

        $gridParams = [
            'GRID_ID' => $arResult['GRID_PARAMS']['ID'],
            'COLUMNS' => array_values($arResult['COLUMNS']),
            'ROWS' =>  $arResult['ROWS'],
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
            'SHOW_ROW_ACTIONS_MENU'     => false,
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
            'ENABLE_FIELDS_SEARCH' => 'Y',
            'SHOW_ROW_CHECKBOXES' => false

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

        ?>
    </div>
</div>


<?if($request->isAjaxRequest() && ($request['GRID_ID'] == $arResult['GRID_PARAMS']['ID'] || $request['params']['FILTER_ID'] == $arResult['GRID_PARAMS']['ID'])){
    die();
}
