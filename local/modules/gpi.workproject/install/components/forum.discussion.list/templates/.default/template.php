<?php
global $USER;
$userId = $USER->getId();
?>


<div class="theme-content">


    <div class="block-title headhead">
        <span class="position-relative">
            Форум
            <div cool-edit-here >
                <?if(array_intersect(['W', 'X'], $arParams['USER_PERMISSIONS'])):?>
                    <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['THEME_CREATE_LINK']?>"></div>
                <?endif;?>
                    <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
                        <div cool-edit-btn data-action-reload="true" data-type="link" data-action="settings" data-link="<?=$arResult['SETTINGS_LINK']?>"></div>
                <?endif;?>
            </div>
        </span>
    </div>

    <div class="ui-list themes-list">
        <?foreach ($arResult['THEMES'] as $theme): ?>
            <div class="row" data-isnew="<?=$theme['IS_NEW']?>" onmouseout="sendWriterReadAction(event)">
                <a class="table-data col-7" href="<?=$theme['LINK']?>"><?=$theme['TITLE']?></a>
                <div class="table-data col-2"><?=$theme['CREATED_TIME']->format('d.m.Y')?></div>
                <div class="table-data col-3 position-relative">
                    <?=$theme['LAST_NAME']?> <?=substr($theme['NAME'], 0, 2)?>.<?=substr($theme['SECOND_NAME'], 0, 2)?>.

                    <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $theme['CREATED_BY'] == $userId)):?>
                        <div cool-edit-here >
                            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$theme['EDIT_LINK']?>"></div>
                            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$theme['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\ForumDiscussionTable')"></div>
                        </div>
                    <?endif;?>
                </div>

            </div>
        <?endforeach;?>

        <?if(!$arResult['THEMES']):?>
            <div class="empty-informer">
                <div class="empty-image"></div>
                <div class="empty-text">Нет данных</div>
            </div>
        <?endif;?>
    </div>

</div>

<script>
    new CoolEditor({
        component : 'rs:forum.discussion.list',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>

