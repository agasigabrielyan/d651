<?php

$arComponentParameters = array(
    "PARAMETERS" => array(
        'TABLE_NAME' => array(
            "NAME" => 'Таблица прав',
            "TYPE" => "STRING",
        ),
        'MODULES_LIST' => array(
            "NAME" => 'Используемые модули',
            "TYPE" => "STRING",
            "MULTIPLE" => "Y",
        ),
        'REF_COLUMN_NAME' => array(
            "NAME" => 'Опознавательное поле',
            "TYPE" => "STRING",
        ),
        'COLUMN_VALUE' => array(
            "NAME" => 'Значение опознавательного поля',
            "TYPE" => "STRING",
        ),
        'RULLS_VALUES' => [
            "NAME" => 'Псевдонимы прав доступа',
            "TYPE" => "STRING",
            'MULTIPLE' => 'Y',
        ]
    ),
);
?>
