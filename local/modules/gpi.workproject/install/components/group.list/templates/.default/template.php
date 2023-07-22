<?php
global $USER;
$userId = $USER->getId();
?>

<div class="grou-list-conteoner">
    <div class="row">
        <div class="col-3">
            <div class="left ico">
                <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="0 0 24 24"><path d="M19.73,16.663A3.467,3.467,0,0,0,20.5,14.5a3.5,3.5,0,0,0-7,0,3.467,3.467,0,0,0,.77,2.163A6.04,6.04,0,0,0,12,18.69a6.04,6.04,0,0,0-2.27-2.027A3.467,3.467,0,0,0,10.5,14.5a3.5,3.5,0,0,0-7,0,3.467,3.467,0,0,0,.77,2.163A6,6,0,0,0,1,22a1,1,0,0,0,1,1H22a1,1,0,0,0,1-1A6,6,0,0,0,19.73,16.663ZM7,13a1.5,1.5,0,1,1-1.5,1.5A1.5,1.5,0,0,1,7,13ZM3.126,21a4,4,0,0,1,7.748,0ZM17,13a1.5,1.5,0,1,1-1.5,1.5A1.5,1.5,0,0,1,17,13Zm-3.873,8a4,4,0,0,1,7.746,0ZM7.2,8.4A1,1,0,0,0,8.8,9.6a4,4,0,0,1,6.4,0,1,1,0,1,0,1.6-1.2,6,6,0,0,0-2.065-1.742A3.464,3.464,0,0,0,15.5,4.5a3.5,3.5,0,0,0-7,0,3.464,3.464,0,0,0,.765,2.157A5.994,5.994,0,0,0,7.2,8.4ZM12,3a1.5,1.5,0,1,1-1.5,1.5A1.5,1.5,0,0,1,12,3Z"/></svg>
            </div>
        </div>

        <div class="col-5 secondary-text">
            Поздравляем, вы уже в Общей группе. Добавляйте участников, присваивайте роли.<br>
            Нужно разделиться? Нет проблем! Cоздавайте отдельные Группы в рамках Проекта и ведите обособленные коммуникации.<br>
            Все, что для этого нужно - нажать кнопку "Создать группу".
        </div>

    </div>


    <div class="block-title headhead">
        <span class="position-relative">
            Список групп и участников

             <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>


                 <div cool-edit-here>
                    <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['CRATE_GROUP_PATH']?>"></div>
                    <div cool-edit-btn data-action-reload="true" data-type="link" data-action="user" data-link="<?=$arResult['EDIT_USERS_PATH']?>"></div>
                </div>

             <?endif;?>

            <script>
                new CoolEditor({
                    component : 'rs:group.list',
                    componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
                    view : 2
                });
            </script>
        </span>
    </div>

    <div class="row">
        <div class="col-12" hidden>
            <div class="group-list ui-list">

                <?foreach ($arResult['GROUPS'] as $group):?>
                    <div class="group-row row">
                        <a class="table-data col-7" href="<?=$group['LINK']?>"><?=$group['TITLE']?></a>
                        <div class="table-data col-2"><?=$group['CREATED_TIME'] ? $group['CREATED_TIME']->format('d.m.Y') : ''?></div>
                        <div class="table-data col-3 position-relative">
                            <?=$group['CREATOR_FIO']?>

                            <?if(array_intersect(['X'], $arResult['PERMISSIONS'][$group['ID']]) || (array_intersect(['W'], $arResult['PERMISSIONS'][$group['ID']]) && $group['CREATED_BY'] == $userId)):?>

                                <div cool-edit-here >
                                    <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$group['EDIT_LINK']?>"></div>
                                    <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$group['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\GroupItemTable')"></div>
                                </div>

                            <?endif;?>

                        </div>
                    </div>

                <?endforeach;?>

                <?if(!$arResult['GROUPS']):?>
                    <div class="empty-informer">
                        <div class="empty-image"></div>
                        <div class="empty-text">Нет данных</div>
                    </div>
                <?endif;?>
            </div>
        </div>



        <div class="col-12">
            <?
            $APPLICATION->IncludeComponent(
                "bitrix:ui.sidepanel.wrapper",
                "",
                [
                    'POPUP_COMPONENT_NAME' => 'rs:project.entity.structure',
                    'CLOSE_AFTER_SAVE' => true,
                    'POPUP_COMPONENT_PARAMS' => array_merge($arParams ?? [], $arResult ?? []),
                ]
            );
            ?>
        </div>
    </div>
</div>

