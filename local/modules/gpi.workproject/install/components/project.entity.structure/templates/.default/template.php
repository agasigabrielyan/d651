<?php
global $USER;

$userId = $USER->getId();

?>

<div class="group-direction-list">
    <?foreach($arResult['DIRECTIONS'] as $direction):?>

        <div class="direction">
            <div class="dir-name"><?=$direction['TITLE']?></div>
            <div class="direction-groups">
                <?foreach ($direction['GROUPS'] as $group):?>
                    <div class="direction-group">
                        <div class="group-name position-relative pr-5">
                            <a href="<?=$group['LINK']?>">
                                <?=$group['TITLE']?>
                            </a>

                            <?if(array_intersect(['X'], $arResult['GROUP_PERMISSIONS'][$group['ID']]) || (array_intersect(['W'], $arResult['GROUP_PERMISSIONS'][$group['ID']]) && $group['CREATED_BY'] == $userId)):?>

                                <div cool-edit-here >
                                    <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$group['EDIT_LINK']?>"></div>
                                    <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$group['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\GroupItemTable')"></div>
                                </div>

                            <?endif;?>

                            <div class="swiper" onclick="this.parentNode.parentNode.classList.toggle('hide')"></div>
                        </div>

                        <div class="ui-table group-users">
                            <?foreach ($group['USERS'] as $user):?>
                                <div class="table-row row">
                                    <div class="table-coll col-5">
                                        <div class="fio <?=$user['IS_NEW'] ==1? 'new' : ''?>">
                                            <?=$user['USER_FIO']?>
                                        </div>

                                    </div>
                                    <div class="table-coll col-2"><?=$user['CATEGORY']?></div>
                                    <div class="table-coll col-3"><?=$user['ORGANIZATION_NAME']?></div>
                                    <div class="table-coll col-2"></div>
                                </div>
                            <?endforeach;?>
                        </div>
                    </div>
                <?endforeach;?>
            </div>
        </div>

    <?endforeach;?>
</div>

<script>
    new CoolEditor({
        component : 'rs:project.entity.structure',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 2
    });
</script>