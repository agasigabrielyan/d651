<?php

foreach ($arResult['RULLS'] as $item){
    if(!$rulls[$item['MAIN_DIRECTION_ID']])
        $rulls[$item['MAIN_DIRECTION_ID']] = [
            'ID' => $item['MAIN_DIRECTION_ID'],
            'TITLE' => $item['MAIN_DIRECTION_TITLE']
        ];

    $rulls[$item['MAIN_DIRECTION_ID']]['ITEMS'][] = $item;
}

$arResult['RULLS'] =  $rulls;