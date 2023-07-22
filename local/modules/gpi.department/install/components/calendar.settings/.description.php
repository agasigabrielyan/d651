<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
    "NAME" => 'Настиройки календаря',
    "ICON" => "/images/icon.gif",
    "COMPLEX" => "N",
    "PATH" => array(
        "ID" => "content",
        "CHILD" => array(
            "ID" => "events_calendar",
            "NAME" => 'Календарь'
        )
    ),
);
?>