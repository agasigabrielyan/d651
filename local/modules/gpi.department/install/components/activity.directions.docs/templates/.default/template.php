<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<div class="anim-menu-gb">

    <input hidden class='entypo-folder' type="radio" id="docs" value="1" name="tractor" checked='checked' />
    <input hidden class='entypo-archive' type="radio" id="local" value="2" name="tractor" />

    <div class='entypo-flag links'>
        <div class='label'>
            <div class="disable-el"></div>
            <label style="border-left: 1px dotted #575654!important;" onclick="setTargetPosition(event)" for="docs">ПИСЬМА, ПРИКАЗЫ, РАСПОРЯЖЕНИЯ</label>
            <label onclick="setTargetPosition(event)" for="local">НОРМАТИВНЫЕ ДОКУМЕНТЫ</label>
        </div>
    </div>

    <section class='docs magic-target' style="position:static">
        <?
        $APPLICATION->IncludeComponent(
            "rs:activity.directions.item.docs",
            "grouped",
            $arParams,
            false,
            ["HIDE_ICONS"=>"Y"]
        );?>
    </section>


    <section class='local magic-target'>
        <?
        $APPLICATION->IncludeComponent(
            "rs:activity.directions.item.local.rulls",
            "grouped",
            $arParams,
            false,
            ["HIDE_ICONS"=>"Y"]
        );?>
    </section>

</div>

<?php
 $APPLICATION->setTitle('Публикации');
?>