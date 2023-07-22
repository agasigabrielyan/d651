<?php

\Bitrix\Main\Loader::IncludeModule('fileman');
CJSCore::init(['bear.file.input', 'ui.buttons', 'ui.buttons.icons']);

    global $USER;
    $userId = $USER->getId();
?>


<div class="entity-comments">
    <div class="title-second">Комментарии:</div>

    <div class="old-comments">

    <?foreach ($arResult['COMMENTS'] as $comment):?>

        <div class="comment">

            <div class="comment-info">
                <div class="author"><?=$comment['CREATOR_FIO']?></div>
                <div class="date">
                    <?=$comment['CREATED_TIME']?><?if($comment['CREATED_TIME'] != $comment['UPDATED_TIME']):?>, изменено <?=$comment['UPDATED_TIME']?><?endif;?>
                </div>
                <div class="body"><?=$comment['BODY']?></div>

                <?if($comment['FILES']):?>
                    <div class="files">
                        Вложения:
                        <?foreach ($comment['FILES'] as $file):?>
                            <a href="<?=$file['LINK']?>" target="_blank" ><?=$file['TITLE']?></a>
                        <?endforeach;?>
                    </div>
                <?endif;?>
            </div>

            <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) || (array_intersect(['W'], $arParams['USER_PERMISSIONS']) && $comment['CREATED_BY'] == $userId)):?>

                <div class="actions">
                    <div class="edit"><?=$arResult['EDIT_BTN']?></div>
                    <div data-id="<?=$comment['ID']?>" class="delete"><?=$arResult['DELETE_BTN']?></div>
                </div>

                <form action="" class="edit-comment hidden">

                    <div class="html">
                        <?
                        $LHE = new CHTMLEditor;
                        $LHE->Show(array(
                            'name' => "BODY",
                            'id' => "BODY{$comment['ID']}",
                            'inputName' => "BODY",
                            'content' => $comment['BODY'],
                            'width' => '100%',
                            'minBodyWidth' => 350,
                            'normalBodyWidth' => 555,
                            'height' => '100',
                            'bAllowPhp' => false,
                            'limitPhpAccess' => false,
                            'autoResize' => true,
                            'autoResizeOffset' => 40,
                            'useFileDialogs' => false,
                            'saveOnBlur' => true,
                            'showTaskbars' => false,
                            'showNodeNavi' => false,
                            'askBeforeUnloadPage' => true,
                            'bbCode' => false,
                            'siteId' => SITE_ID,
                            'controlsMap' => array(
                                array('id' => 'Bold', 'compact' => true, 'sort' => 80),
                                array('id' => 'Italic', 'compact' => true, 'sort' => 90),
                                array('id' => 'Underline', 'compact' => true, 'sort' => 100),
                                array('id' => 'Strikeout', 'compact' => true, 'sort' => 110),
                                array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120),
                                array('id' => 'Color', 'compact' => true, 'sort' => 130),
                                array('id' => 'FontSelector', 'compact' => false, 'sort' => 135),
                                array('id' => 'FontSize', 'compact' => false, 'sort' => 140),
                                array('separator' => true, 'compact' => false, 'sort' => 145),
                                array('id' => 'OrderedList', 'compact' => true, 'sort' => 150),
                                array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160),
                                array('id' => 'AlignList', 'compact' => false, 'sort' => 190),
                                array('separator' => true, 'compact' => false, 'sort' => 200),
                                array('id' => 'InsertLink', 'compact' => true, 'sort' => 210),
                                array('id' => 'InsertImage', 'compact' => false, 'sort' => 220),
                                array('id' => 'InsertVideo', 'compact' => true, 'sort' => 230),
                                array('id' => 'InsertTable', 'compact' => false, 'sort' => 250),
                                array('separator' => true, 'compact' => false, 'sort' => 290),
                                array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310),
                                array('id' => 'More', 'compact' => true, 'sort' => 400)
                            ),
                        ));
                        ?>

                        <div class="ui-btn ui-btn-sm ui-btn-primary loadComment">Сохранить</div>

                        <input type="file" data-files='<?=Bitrix\Main\Web\Json::encode($comment['FILES'])?>' name="FILES" multiple class="uploadFiles form-target">
                        <input type="hidden" name="ENTITY" value="<?=$arParams['ENTITY']?>" class="form-target">
                        <input type="hidden" name="ID" value="<?=$comment['ID']?>" class="form-target">
                        <input type="hidden" name="ENTITY_ID" value="<?=$arParams['ENTITY_ID']?>" class="form-target">

                    </div>
                </form>

            <?endif;?>
        </div>

    <?endforeach;?>

    </div>

    <form action="" class="new-comment">
        <div class="ui-btn ui-btn-icon-chat d-table">Написать новый комментарий</div>

        <div class="ad-comment hidden">
            <?
            $LHE = new CHTMLEditor;
            $LHE->Show(array(
                'name' => "BODY",
                'id' => 'BODY',
                'inputName' => "BODY",
                'content' => '',
                'width' => '100%',
                'minBodyWidth' => 350,
                'maxBodyWidth' => 600,
                'normalBodyWidth' => 555,
                'height' => '100',
                'bAllowPhp' => false,
                'limitPhpAccess' => false,
                'autoResize' => true,
                'autoResizeOffset' => 40,
                'useFileDialogs' => false,
                'saveOnBlur' => true,
                'showTaskbars' => false,
                'showNodeNavi' => false,
                'askBeforeUnloadPage' => true,
                'bbCode' => false,
                'siteId' => SITE_ID,
                'controlsMap' => array(
                    array('id' => 'Bold', 'compact' => true, 'sort' => 80),
                    array('id' => 'Italic', 'compact' => true, 'sort' => 90),
                    array('id' => 'Underline', 'compact' => true, 'sort' => 100),
                    array('id' => 'Strikeout', 'compact' => true, 'sort' => 110),
                    array('id' => 'RemoveFormat', 'compact' => true, 'sort' => 120),
                    array('id' => 'Color', 'compact' => true, 'sort' => 130),
                    array('id' => 'FontSelector', 'compact' => false, 'sort' => 135),
                    array('id' => 'FontSize', 'compact' => false, 'sort' => 140),
                    array('separator' => true, 'compact' => false, 'sort' => 145),
                    array('id' => 'OrderedList', 'compact' => true, 'sort' => 150),
                    array('id' => 'UnorderedList', 'compact' => true, 'sort' => 160),
                    array('id' => 'AlignList', 'compact' => false, 'sort' => 190),
                    array('separator' => true, 'compact' => false, 'sort' => 200),
                    array('id' => 'InsertLink', 'compact' => true, 'sort' => 210),
                    array('id' => 'InsertImage', 'compact' => false, 'sort' => 220),
                    array('id' => 'InsertVideo', 'compact' => true, 'sort' => 230),
                    array('id' => 'InsertTable', 'compact' => false, 'sort' => 250),
                    array('separator' => true, 'compact' => false, 'sort' => 290),
                    array('id' => 'Fullscreen', 'compact' => false, 'sort' => 310),
                    array('id' => 'More', 'compact' => true, 'sort' => 400)
                ),
            ));
            ?>

            <div class="ui-btn ui-btn-sm ui-btn-primary loadComment">Сохранить</div>

            <input type="file" name="FILES" multiple class="uploadFiles form-target">
            <input type="hidden" name="ENTITY" value="<?=$arParams['ENTITY']?>" class="form-target">
            <input type="hidden" name="ID" value="" class="form-target">
            <input type="hidden" name="ENTITY_ID" value="<?=$arParams['ENTITY_ID']?>" class="form-target">

        </div>
    </form>
</div>