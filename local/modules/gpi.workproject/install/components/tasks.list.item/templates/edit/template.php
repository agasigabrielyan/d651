<?php
CJSCore::init(['sidepanel', 'sidepanel.reference.link.save', 'ui.entity-selector','bear.file.input']);
\Bitrix\Main\Loader::IncludeModule('fileman');

$APPLICATION->setTitle("Задача {$arResult['TASK']['TITLE']}");
global $USER;

switch (MAIN_USER_THEME) {
    case 'WHITE':
        $APPLICATION->SetAdditionalCSS($this->GetFolder() . '/white_theme.css');
        break;
}?>


<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
    <form class="cool-form">
        <legend>
            <label for="TITLE">Название*</label>
            <input class="field" required type="text" value="<?=$arResult['TASK']['TITLE']?>" name="TITLE">
        </legend>

        <legend>
            <label for="PRODUCER">Постановщик</label>
            <input class="field" required type="number" value="<?=$arResult['TASK']['PRODUCER']?>" name="PRODUCER">
        </legend>

        <legend>
            <label for="PROVIDER">Исполнитель</label>
            <input class="field" required type="number" value="<?=$arResult['TASK']['PROVIDER']?>" name="PROVIDER">
        </legend>

        <legend>
            <label for="GROUP_ID">Группа исполнителя</label>
            <select class="field" required  name="GROUP_ID">
                <?foreach ($arResult['PROVIDER_GROUPS'] as $groupId):?>
                    <option <?=$arResult['TASK']['GROUP_ID'] == $groupId? 'selected' : ''?> value="<?=$groupId?>"><?=$arResult['PROJECT_GROUPS'][$groupId]?></option>
                <?endforeach;?>
            </select>
        </legend>

        <legend >
            <label for="DESCRIPTION">Описание</label>
            <?
            $LHE = new CHTMLEditor;
            $LHE->Show(array(
                'name' => "DESCRIPTION",
                'id' => 'DESCRIPTION',
                'inputName' => "DESCRIPTION",
                'content' => $arResult['TASK']['DESCRIPTION'],
                'width' => '100%',
                'minBodyWidth' => 350,
                'normalBodyWidth' => 555,
                'height' => '200',
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
        </legend>

        <legend>
            <input type="file" name="FILES" multiple class="uploadFiles field" >
        </legend>

        <legend>
            <label for="PREORITY">Приоритет</label>
            <select class="field" name="PREORITY" >
                <option <?=$arResult['TASK']['PREORITY'] == 1 ? 'selected' : ''?> value="1">Низкий</option>
                <option <?=$arResult['TASK']['PREORITY'] == 2 ? 'selected' : ''?> value="2">Средний</option>
                <option <?=$arResult['TASK']['PREORITY'] == 3 ? 'selected' : ''?> value="3">Высокий</option>
            </select>
        </legend>

        <legend>
            <label for="CONTROL_DATE" onclick="BX.calendar({node:  this.parentNode.querySelector('input') , field:  this.parentNode.querySelector('input') , bTime:  true , bSetFocus:  true});">Контрольный срок</label>
            <input class="field" type="text" value="<?=$arResult['TASK']['CONTROL_DATE']?>" name="CONTROL_DATE" onclick="BX.calendar({node:  this , field:  this , bTime:  true , bSetFocus:  true});" >
        </legend>

        <legend>
            <label for="LABOR_COST">Трудозатраты (день)</label>
            <input class="field" type="number" <?=$USER->getId() != $arResult['TASK']['PROVIDER']? 'disabled' : ''?> value="<?=$arResult['TASK']['LABOR_COST']?>" name="LABOR_COST">
        </legend>

        <legend class="d-flex justify-content-end">
            <label class="p-1" for="IS_CONDITIONAL_APPROVAL">Условное согласование</label>
            <input style="width: 20px;" class="field" type="checkbox" <?=$arResult['TASK']['IS_CONDITIONAL_APPROVAL'] == 1? 'checked' : ''?> name="IS_CONDITIONAL_APPROVAL">
        </legend>

        <legend class="d-flex justify-content-end">
            <label class="p-1" for="IS_IMPORTANT">Срочно</label>
            <input style="width: 20px;" class="field" type="checkbox" <?=$arResult['TASK']['IS_IMPORTANT'] == 1? 'checked' : ''?> name="IS_IMPORTANT">
        </legend>


        <input class="field" hidden type="text" name="ID" value="<?=$arParams['VARIABLES']['task_id']?>">
        <input class="field" hidden type="text" name="PROJECT_ID" value="<?=$arParams['~VARIABLES']['project_id']?>">
    </form>
</div>

<div class="ui-btn-container ui-btn-container-center">
    <div onclick="loadEntity()" class="feed-btn">Применить</div>
</div>

<script>
    BX.ready(function(){
        new BearFileInput(document.querySelector('input[type=file]'), <?=\Bitrix\Main\Web\Json::encode($arResult['TASK']['FILES'])?>);
    })
</script>