<?php
global $USER;
$userId = $USER->getId();
?>

<div class="control">

    <?php
    $APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
        'FILTER_ID' => $arResult['FILTER_ID'],
        'FILTER' => $arResult['FILTER'],
        'ENABLE_LIVE_SEARCH' => true,
        'ENABLE_LABEL' => true
    ],
        false,
        array("HIDE_ICONS" => false)
    );
    ?>

    <?if(array_intersect(['W', 'X'], $arParams['USER_PERMISSIONS']) ):?>

        <div onclick="openSidePanel('<?=$arResult['CREATE_TASK_LINK']?>', 600, 'refreshTasksListContent();')" class="ui-btn-sm feed-btn">Создать задачу</div>

    <?endif;?>

</div>

<div class="ui-list white task-list">
    <? foreach ($arResult['TASKS'] as $task):?>
        <div class="row">
            <div class="table-data col-2 linked" onclick="openSidePanel('<?=$task['ID']?>/', 600)"><?=$task['TITLE']?></div>
            <div class="table-data col-2"><?=$task['PRODUCER_FULL_NAME']?></div>
            <div class="table-data col-2"><?=$task['PROVIDER_FULL_NAME']?></div>
            <div class="table-data col-2"><?=$task['CREATED_TIME']?></div>
            <div class="table-data col-2"><?=$task['STATUS']?></div>
            <div class="table-data col-2"><?=$task['PREORITY']?></div>

            <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])
            ||
            (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $task['CREATED_BY'] == $userId)
            ):?>
                <div class="actions">
                    <div onclick="openSidePanel('<?=$task['ID']?>/edit/', 600, 'refreshTasksListContent()');" class="edit"><?=$arResult['EDIT_BTN']?></div>
                    <div onclick="showConfirmToDeleteTask('<?=$task['ID']?>');" class="delete"><?=$arResult['DELETE_BTN']?></div>

                </div>

            <?endif;?>
        </div>
    <?endforeach;?>

    <?if(!$arResult['TASKS']):?>
        <div class="empty-informer">
            <div class="empty-image"></div>
            <div calss="empty-text">Нет данных</div>
        </div>
    <?endif;?>

</div>
