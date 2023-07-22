<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Отраслевые сервисы");
?>
<?php
$APPLICATION->IncludeComponent(
    "rs:industrial.services",
    "",
    [
        'IBLOCK_ID' => 1
    ]
);

?>
<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>