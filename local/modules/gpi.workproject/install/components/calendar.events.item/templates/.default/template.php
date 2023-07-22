<?php

global $APPLICATION;
global $USER;
$APPLICATION->showHead();
?>


<script>
    const event = <?=CUtil::PhpToJSObject($arResult['event'])?>;
    const userId = "<?=$USER->getId()?>";
    BX.ready(function(){
        window.calendarRS = new RSCalendarItem(event);
    })
</script>


<form class="element-form">
    <div class="row">
        <input class="field" hidden value="<?=$arResult['event']['ID']?>" name="ID">
        <input class="field" value="<?=$arResult['event']['CALENDAR_ID'] ?? $arParams['CALENDAR_ID']?>" hidden name="CALENDAR_ID">

        <?if($arParams['CALENDAR_ID'] != 1):?>

            <div class="private-swicher">
                <input hidden type="number" name="IS_PUBLICK" class="field">
                <label class="checkbox-rs">
                    <input <?=$arResult['event']['IS_PUBLICK'] == 1? 'checked' : ''?> onchange="this.closest('.private-swicher').querySelector('[name=IS_PUBLICK]').value = this.checked? 1 : 0;" type="checkbox">
                    <span class="checkbox-rs-switch"></span>
                </label>
            </div>
        <?endif;?>

        <select onchange="window.calendarRS.showEventTypeProps()" class="field form-type-selector" name="TYPE">
            <option <?=$arResult['event']['TYPE'] == 'EVENT'? 'selected' : '' ?> value="EVENT">Событие</option>
            <option <?=$arResult['event']['TYPE'] == 'TASK'? 'selected' : '' ?> value="TASK">Задача</option>
            <option <?=$arResult['event']['TYPE'] == 'WORK'? 'selected' : '' ?> value="WORK">Работа</option>
        </select>

        <div class="col-12">
            <div class="legend">
                <label for="TITLE">Тема</label>
                <input class="field" value="<?=$arResult['event']['TITLE']?>" name="TITLE" type="text"  required>
            </div>
        </div>

        <div class="col-12">
            <div class="legend">
                <label for="DESCRIPTION">Описание</label>
                <textarea  class="field" name="DESCRIPTION"> <?=$arResult['event']['DESCRIPTION']?></textarea>
            </div>
        </div>

        <div data-event-toggle-prop data-target="EVENT" class="col-12 hidden">
            <div class="legend">
                <label for="PROVIDER">Организатор</label>
                <input class="field" value="<?=$arResult['event']['PROVIDER']?>" data-user-select-render name="PROVIDER" type="text" required>
            </div>
        </div>

        <div data-event-toggle-prop data-target="TASK" class="col-12 hidden">
            <div class="legend">
                <label for="GUARANTOR">Поручитель</label>
                <input class="field" value="<?=$arResult['event']['GUARANTOR']?>" data-user-select-render name="GUARANTOR" type="text" required>
            </div>
        </div>

        <div data-event-toggle-prop data-target="TASK,WORK" class="col-12 hidden">
            <div class="legend">
                <label for="EXECUTOR">Исполнитель</label>
                <input class="field" value="<?=$arResult['event']['EXECUTOR']?>" data-user-select-render name="EXECUTOR" type="text" required>
            </div>
        </div>

        <div class="col-12">
            <div class="legend">
                <label for="STARTED"  onclick="BX.calendar({node:  this.parentNode.querySelector('input') , field:  this.parentNode.querySelector('input') , bTime:  true , bSetFocus:  true});">Дата начала</label>
                <input class="field" value="<?=$arResult['event']['STARTED']?>" name="STARTED" type="text"  onclick="BX.calendar({node:  this , field:  this , bTime:  true , bSetFocus:  true});" required>
            </div>
        </div>

        <div class="col-12">
            <div class="legend">
                <label for="ENDED"  onclick="BX.calendar({node:  this.parentNode.querySelector('input') , field:  this.parentNode.querySelector('input') , bTime:  true , bSetFocus:  true});">Дата окончания</label>
                <input class="field" value="<?=$arResult['event']['ENDED']?>" name="ENDED" type="text"  onclick="BX.calendar({node:  this , field:  this , bTime:  true , bSetFocus:  true});" required>
            </div>
        </div>

        <div data-event-toggle-prop data-target="TASK,WORK" class="col-12 hidden">
            <div class="legend">
                <label for="FACT_STARTED"  onclick="BX.calendar({node:  this.parentNode.querySelector('input') , field:  this.parentNode.querySelector('input') , bTime:  true , bSetFocus:  true});">Дата начала факт</label>
                <input class="field" value="<?=$arResult['event']['FACT_STARTED']?>" name="FACT_STARTED" type="text"  onclick="BX.calendar({node:  this , field:  this , bTime:  true , bSetFocus:  true});">
            </div>
        </div>

        <div data-event-toggle-prop data-target="TASK,WORK" class="col-12 hidden">
            <div class="legend">
                <label for="FACT_ENDED"  onclick="BX.calendar({node:  this.parentNode.querySelector('input') , field:  this.parentNode.querySelector('input') , bTime:  true , bSetFocus:  true});">Дата окончания факт</label>
                <input class="field" value="<?=$arResult['event']['FACT_ENDED']?>"  name="FACT_ENDED" type="text"  onclick="BX.calendar({node:  this , field:  this , bTime:  true , bSetFocus:  true});">
            </div>
        </div>

        <div class="col-12">
            <div class="legend">
                <label >Материалы</label>

                <input multiple type="file" id="FILES" name="FILES" class="uploadFiles">
            </div>
        </div>
    </div>

    <div class="ui-btn-container d-flex">
        <div onclick="window.calendarRS.loadData()" class="feed-btn">Сохранить</div>

        <?if($arResult['event']['ID']):?>
            <div onclick="window.calendarRS.deleteEvent(<?=$arResult['event']['ID']?>)" class="ui-btn ui-btn-light-border">Удалить</div>
        <?endif;?>
    </div>
</form>

