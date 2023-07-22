<?php
global $APPLICATION
?>


<div class="entity-settings">

    <input type="hidden" name="PROJECT_ID" value="<?=$arResult['PROJECT']['ID']?>">

    <legend>
        <label for="TITLE">Название</label>
        <input type="text" name="TITLE" value="<?=$arResult['PROJECT']['TITLE']?>">
    </legend>

    <legend>
        <label for="DIRECTOR_ID">Руководитель</label>
        <input class="field" required type="number" value="<?=$arResult['PROJECT']['DIRECTOR_ID']?>" name="DIRECTOR_ID">
    </legend>


    <legend>
        <label>Права доступа</label>

        <?

        $APPLICATION->IncludeComponent(
            "rs:entity.user.permission.configator",
            "",
            [
                'TABLE_NAME' => 'Gpi\Workproject\Orm\ProjectUserPermissionTable',
                'MODULES_LIST' => [
                    'gpi.workproject'
                ],
                'REF_COLUMN_NAME' => 'PROJECT_ID',
                'COLUMN_VALUE' => $arResult['PROJECT']['ID'],
                'RULLS_VALUES' => [
                    'X' => 'Полный доступ',
                    'W' => 'Изменение',
                    'R' => 'Чтение',
                ],
                'PROJECT_GROUP_EXISTS' => $arParams['PROJECT_GROUP_EXISTS'],
                'PROJECT_GROUP_EXISTS_GROUUP_ID' => $arParams['PROJECT_GROUP_EXISTS_GROUUP_ID'],
            ]
        );
        ?>
    </legend>

    <div class="ui-btn-container ui-btn-container-center">
        <div class="ui-btn ui-btn-success" onclick="saveConfig()">Сохранить</div>
    </div>


</div>