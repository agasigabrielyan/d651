<?php

global $USER;
$userId = $USER->getId();
?>

<div class="activities">

    <div class="block-title headhead">
        <span class="position-relative">Направления проекта

            <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
                <div cool-edit-here >
                    <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['AD_LINK']?>"></div>
                </div>
            <?endif;?>

        </span>
    </div>




    <div class="project-activity-directions ui-list">

        <?foreach($arResult['ACTIVITY_DIRECTIONS'] as $direction):?>

            <div class="project-row row" data-new-id="<?=$direction['NEW_ID']?>" data-isnew="<?=$direction['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">
                <div class="table-data col-10"><?=$direction['TITLE']?></div>
                <div class="table-data col-2  position-relative">
                    <?=$direction['CREATED_TIME'] ? $direction['CREATED_TIME']->format('d.m.Y') : ''?>
                    <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])
                        ||
                        (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $direction['CREATED_BY'] == $userId)
                        ||
                        $userId == $direction['DIRECTOR_ID']
                    ):?>
                        <div cool-edit-here >
                            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$direction['EDIT_LINK']?>"></div>
                            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$direction['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\ProjectDirectionTable')"></div>
                        </div>
                    <?endif;?>
                </div>

            </div>

        <?endforeach;?>

        <?if(!$arResult['ACTIVITY_DIRECTIONS']):?>
            <div class="empty-informer">
                <div class="empty-image"></div>
                <div class="empty-text">Нет данных</div>
            </div>
        <?endif;?>

    </div>
</div>

<script>
    new CoolEditor({
        component : 'rs:project.list.item.directions',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>