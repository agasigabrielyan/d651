<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<div class="anim-menu-gb">

    <input hidden class='entypo-folder' type="radio" id="events" value="1" name="tractor" checked='checked' />
    <input hidden class='entypo-archive' type="radio" id="anounsments" value="2" name="tractor" />
    <input hidden class='entypo-box' type="radio" id="importants" value="3" name="tractor" />

    <div class='entypo-flag links'>
        <div class='label'>
            <div class="disable-el"></div>
            <label style="border-left: 1px dotted #575654!important;" onclick="setTargetPosition(event)" for="events">События</label>
            <label onclick="setTargetPosition(event)" for="anounsments">Объявления</label>
            <label onclick="setTargetPosition(event)" for="importants">Важно</label>
        </div>
    </div>

    <section class='events magic-target' style="position:static">
        <?
        $APPLICATION->IncludeComponent(
            "rs:activity.directions.item.events",
            "",
            $arParams,
            false,
            ["HIDE_ICONS"=>"Y"]
        );?>
    </section>


    <section class='anounsments magic-target'>
        <?
        $APPLICATION->IncludeComponent(
            "bitrix:ui.sidepanel.wrapper",
            "",
            [
                'POPUP_COMPONENT_NAME' => "rs:ad.union",
                'CLOSE_AFTER_SAVE' => true,
                'POPUP_COMPONENT_PARAMS' => [
                    'SEF_FOLDER' => $arParams['SEF_FOLDER'].str_replace(['#main_id#', '#direction_id#'],[$arParams['VARIABLES']['main_id'], $arParams['VARIABLES']['direction_id']],$arParams['URL_TEMPLATES']['detail_direction_item_ads']),
                    'UNION_ID' => $arResult['DIRECTION_ADS'],
                ],
            ]
        ); ?>
    </section>


    <section class='importants magic-target'>
        <?$APPLICATION->IncludeComponent(
            "rs:activity.directions.item.importants",
            "",
            array_merge($arParams,[
                'SHOW_ALL_CONTENT' => 'Y'
            ]),
            false,
            ["HIDE_ICONS"=>"Y"]
        );?>
    </section>
</div>

<?php
 $APPLICATION->setTitle('Публикации');
?>