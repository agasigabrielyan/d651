<?
global $USER;
$userId = $USER->getId();

?>

<div class="<?=$arResult['GRID_ID']?>">
    <div class="row">
        <div class="col-3">
            <div class="left ico">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="800px" height="800px" viewBox="0 0 24 24" version="1.1">
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="ic-bag" fill-rule="nonzero" fill="#4A4A4A">
                            <path d="M4,9.0085302 L4,16.9914698 C4,17.5484013 4.4481604,18 4.99508929,18 L19.0049107,18 C19.5533259,18 20,17.5502218 20,16.9914698 L20,9.0085302 C20,8.45159872 19.5518396,8 19.0049107,8 L4.99508929,8 C4.44667411,8 4,8.4497782 4,9.0085302 Z M17,6 C17,5.44771525 17.4438648,5 18,5 C18.5522847,5 19,5.44386482 19,6 L19.0049107,6 C20.6598453,6 22,7.35043647 22,9.0085302 L22,16.9914698 C22,18.6517027 20.6610078,20 19.0049107,20 L4.99508929,20 C3.34015473,20 2,18.6495635 2,16.9914698 L2,9.0085302 C2,7.34829734 3.33899222,6 4.99508929,6 L5,6 C5,5.44771525 5.44386482,5 6,5 C6.55228475,5 7,5.44386482 7,6 L8.12601749,6 C8.57006028,4.27477279 10.1361606,3 12,3 C13.8638394,3 15.4299397,4.27477279 15.8739825,6 L17,6 Z M10.2675644,6 L13.7324356,6 C13.3866262,5.40219863 12.7402824,5 12,5 C11.2597176,5 10.6133738,5.40219863 10.2675644,6 Z" id="Rectangle">

                            </path>
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <div class="col-5 secondary-text">
            Вы решили создать Проект?<br> Отличная идея! Добавляйте Участников, разделяйтесь, при необходимости, на Группы, обменивайтесь материалами и мнениями, фиксируйте Задачи. До всего этого один шаг: перейдите на вкладку "Группы".
        </div>

        <div class="col-4 position-relative">

                <div cool-edit-here >
                    <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $arResult['PROJECT']['CREATED_BY'] == $userId)):?>
                        <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$arResult['PROJECT']['EDIT_LINK']?>"></div>
                        <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arResult['PROJECT']['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\ProjectTable')"></div>
                    <?endif;?>
                    <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
                        <div cool-edit-btn data-action-reload="true" data-type="link" data-action="settings" data-link="<?=$arResult['SETTINGS_PATH']?>"></div>
                    <?endif;?>
                </div>
        </div>
    </div>

    <script>
        new CoolEditor({
            component : 'rs:project.list.item',
            componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
            view : 2
        });
    </script>


    <div class="main-second-block row">
        <div class="left col-7">
            <div class="legend">
                <div class="rigth">

                    <div class="item">
                        <div class="capital">Цель:</div>
                        <div class="secondary"><?=$arResult['PROJECT']['TARGET']?></div>
                    </div>


                    <div class="item">
                        <div class="capital">Описание:</div>
                        <div class="secondary project-description">
                            <?=$arResult['PROJECT']['DESCRIPTION']?>
                        </div>
                        <div class="toggleHeight" hidden onclick="this.parentNode.classList.toggle('show-me')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="23px" height="22px" viewBox="0 0 24 24" fill="none">
                                <title>Развернуть</title>
                                <path d="M16.19 2H7.81C4.17 2 2 4.17 2 7.81V16.18C2 19.83 4.17 22 7.81 22H16.18C19.82 22 21.99 19.83 21.99 16.19V7.81C22 4.17 19.83 2 16.19 2ZM14.79 12.53L11.26 16.06C11.11 16.21 10.92 16.28 10.73 16.28C10.54 16.28 10.35 16.21 10.2 16.06C9.91 15.77 9.91 15.29 10.2 15L13.2 12L10.2 9C9.91 8.71 9.91 8.23 10.2 7.94C10.49 7.65 10.97 7.65 11.26 7.94L14.79 11.47C15.09 11.76 15.09 12.24 14.79 12.53Z" fill="#9fd0f1"></path>
                            </svg>
                        </div>
                    </div>



                    <div class="item">
                        <div class="capital">Автор:</div>
                        <div class="secondary"><?=$arResult['PROJECT']['CREATOR_FIO']?></div>
                    </div>
                </div>
            </div>

            <?$APPLICATION->IncludeComponent(
                "bitrix:ui.sidepanel.wrapper",
                "",
                [
                    'POPUP_COMPONENT_NAME' => 'rs:project.list.item.directions',
                    'CLOSE_AFTER_SAVE' => true,
                    'POPUP_COMPONENT_PARAMS' => array_merge($arParams ?? [], $arResult ?? []),
                ]
            );?>
        </div>

        <div class="right col-5">
            <?$APPLICATION->IncludeComponent(
                "bitrix:ui.sidepanel.wrapper",
                "",
                [
                    'POPUP_COMPONENT_NAME' => 'rs:forum.discussion',
                    'CLOSE_AFTER_SAVE' => true,
                    'POPUP_COMPONENT_PARAMS' => ['SET_BRANDCAMPS' => 'N', 'FORUM_ID' => $arResult['PROJECT']['FORUM_ID'], 'SEF_FOLDER' => $arResult['DISCUSSION_PATH']],
                ]
            );?>
        </div>
    </div>

</div>
