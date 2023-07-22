<?
global $USER;
$userId = $USER->getId();
?>




<div class="main-second-block row position-relative pt-3">

    <div cool-edit-here >
        <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $arResult['GROUP']['CREATED_BY'] == $userId)):?>
            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$arResult['GROUP']['EDIT_LINK']?>"></div>
            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arResult['GROUP']['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\GroupItemTable')"></div>
        <?endif;?>
        <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS'])):?>
            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="settings" data-link="<?=$arResult['SETTINGS_PATH']?>"></div>
        <?endif;?>
    </div>

    <div class="left col-7">
       <div class="users" style="padding-right: 30px">
           <div class="block-title-second headhead">
               <span>Участники</span>
           </div>
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

    <div class="right col-5">
        <?$APPLICATION->IncludeComponent(
            "bitrix:ui.sidepanel.wrapper",
            "",
            [
                'POPUP_COMPONENT_NAME' => 'rs:forum.discussion',
                'CLOSE_AFTER_SAVE' => true,
                'POPUP_COMPONENT_PARAMS' => ['SET_BRANDCAMPS' => 'N', 'FORUM_ID' => $arResult['GROUP']['FORUM_ID'], 'SEF_FOLDER' => $arResult['DISCUSSION_PATH']],
            ]
        );?>
    </div>
</div>