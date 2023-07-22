<?php
global $USER;
$userId = $USER->getId();
?>


<div class="theme-content">

    <div class="theme">

        <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $arResult['THEME']['CREATED_BY'] == $userId)):?>
            <div cool-edit-here >
                <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$arResult['THEME']['EDIT_LINK']?>"></div>
                <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$arResult['THEME']['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\ForumDiscussionTable')"></div>
            </div>
        <?endif;?>

        <?=$arResult['THEME']['TITLE']?>


        <div class="CREATED_BY d-flex">
            <div class="avatar">
                <img class="photo" src="/local/templates/deps_layout/images/user_preview_default.png">
            </div>
            <div class="post-info">
                <div class="fio">
                    <?=$arResult['THEME']["CREATED_BY_FIO"]?>
                </div>
                <div class="created">
                    <?=$arResult['THEME']['CREATED_TIME'] ? $arResult['THEME']['CREATED_TIME']->format('d.m.Y H:i') : ''?>
                </div>
            </div>
        </div>

        <div class="description">
            <?=$arResult['THEME']['DESCRIPRION']?>
        </div>
        <?if($arResult['THEME']['FILES']):?>

            <div class="files">
                Вложения:
                <?foreach ($arResult['THEME']['FILES'] as $file):?>
                    <a target="_blank" href="<?=$file['PATH']?>" ><?=$file['ORIGINAL_NAME']?></a>
                <?endforeach;?>
            </div>

        <?endif;?>

    </div>

    <div class="comment-list position-relative pt-5">

        <?if(array_intersect(['W', 'X'], $arParams['USER_PERMISSIONS'])):?>

            <div cool-edit-here class="add-link">
                <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$arResult['ADD_MESSAGE_LINK']?>"></div>
            </div>

        <?endif;?>

        <?foreach ($arResult['MESSAGES'] as $message):?>
            <div class="coment-container" >
                <div class="comment" id="comment_<?=$message['ID']?>">

                    <div class="CREATED_BY"><?=$message['CREATED_BY_FIO']?></div>
                    <div class="date"><?=$message['CREATED_TIME'] ? $message['CREATED_TIME']->format('d.m.Y H:i') : ''?></div>
                    <div class="text"><?=$message['MESSAGE_BODY']?></div>

                    <?if($message['FILES']):?>

                        <div class="files">
                            Вложения:
                            <?foreach ($message['FILES'] as $file):?>
                                <a target="_blank" href="<?=$file['PATH']?>" ><?=$file['ORIGINAL_NAME']?></a>
                            <?endforeach;?>
                        </div>

                    <?endif;?>

                    <?if(array_intersect(['W', 'X'], $arParams['USER_PERMISSIONS'])):?>
                        <div cool-edit-here class="answer">
                            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$message['ANSWER_LINK']?>"></div>
                        </div>
                    <?endif;?>

                    <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $message['CREATED_BY'] == $userId)):?>

                        <div cool-edit-here >
                            <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$message['EDIT_LINK']?>"></div>
                            <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$message['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\ForumDiscussionMessageTable')"></div>
                        </div>

                    <?endif;?>

                </div>

                <?foreach ($message['COMMENTS'] as $comment):?>
                    <div class="coment-container second_lvl">
                        <div class="comment" id="comment_<?=$comment['ID']?>">
                            <div class="CREATED_BY"><?=$comment['CREATED_BY_FIO']?></div>
                            <div class="date"><?=$message['CREATED_TIME'] ? $message['CREATED_TIME']->format('d.m.Y H:i') : ''?></div>
                            <div class="text">
                                <span class="parent_link" target="comment_<?=$comment['PARENT_ID']?>" onclick="showParent('comment_<?=$comment['PARENT_ID']?>')">
                                    <?
                                    $parentUser = $arResult['MESSAGE_USERS'][$comment['PARENT_ID']];
                                    ?>
                                    <!--?=$parentUser['LAST_NAME']?> <?=substr($parentUser['NAME'], 0, 2)?>.<?=substr($parentUser['SECOND_NAME'], 0, 2)?>.<br-->
                                    В ответ на комментарий
                                </span>
                                <?=$comment['MESSAGE_BODY']?>

                            </div>

                            <?if($comment['FILES']):?>

                                <div class="files">
                                    Вложения:
                                    <?foreach ($comment['FILES'] as $file):?>
                                        <a target="_blank" href="<?=$file['PATH']?>" ><?=$file['ORIGINAL_NAME']?></a>
                                    <?endforeach;?>
                                </div>

                            <?endif;?>

                            <?if(array_intersect(['W', 'X'], $arParams['USER_PERMISSIONS'])):?>
                                <div cool-edit-here class="answer">
                                    <div cool-edit-btn data-action-reload="true" data-type="link" data-action="add" data-link="<?=$comment['ANSWER_LINK']?>"></div>
                                </div>
                            <?endif;?>

                            <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $comment['CREATED_BY'] == $userId)):?>

                                <div cool-edit-here>
                                    <div cool-edit-btn data-action-reload="true" data-type="link" data-action="edit" data-link="<?=$comment['EDIT_LINK']?>"></div>
                                    <div cool-edit-btn data-action-reload="true" data-type="script" data-action="delete" data-script="window.coolEditor.deleteEntity(<?=$comment['ID']?>, 'gpi.workproject', 'table', 'Gpi\\Workproject\\Orm\\ForumDiscussionMessageTable')"></div>
                                </div>

                            <?endif;?>

                        </div>
                    </div>
                <?endforeach;?>
            </div>
        <?endforeach;?>
    </div>


</div>

<script>
    new CoolEditor({
        component : 'rs:forum.discussion.list.item',
        componentParams: <?=\Bitrix\Main\Web\Json::encode($arParams)?>,
        view : 1
    });
</script>



