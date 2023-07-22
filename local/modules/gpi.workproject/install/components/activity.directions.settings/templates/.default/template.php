<?php
global $APPLICATION
?>


<div class="entity-settings">
    <legend>
        <label>Права доступа</label>

        <?

        $APPLICATION->IncludeComponent(
            "rs:entity.user.permission.configator",
            "",
            [
                'TABLE_NAME' => 'Gpi\Workproject\Orm\ActivityDirectionPermissionTable',
                'MODULES_LIST' => [
                        'gpi.workproject'
                ],
                'RULLS_VALUES' => [
                    'X' => 'Полный доступ',
                    'W' => 'Изменение',
                    'R' => 'Чтение',
                ],
            ]
        );
        ?>
    </legend>

    <div class="ui-btn-container ui-btn-container-center">
        <div class="ui-btn ui-btn-success" onclick="saveConfig()">Сохранить</div>
    </div>


</div>