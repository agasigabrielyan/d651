<?php require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';
$APPLICATION->showHead();

$request = Bitrix\Main\Context::getCurrent()->getRequest();

CJSCore::Init('selectize');
\Bitrix\Main\UI\Extension::load("ui.notification");

?>

<form class="cool-form">
    <input class="form-target" type="hidden" id="CREATED_BY" value="<?=$arResult['USER_DATA']['ID']?>">

    <legend>
        <label for="UF_FIO">Фио</label>
        <input class="form-target" value="<?=$arResult['USER_DATA']['LAST_NAME']?> <?=$arResult['USER_DATA']['NAME']?> <?=$arResult['USER_DATA']['SECOND_NAME']?>" type="name" id="UF_FIO">
    </legend>

    <legend>
        <label for="UF_EMAIL">E-mail</label>
        <input class="form-target" value="<?=$arResult['USER_DATA']['EMAIL']?>" type="email" id="UF_EMAIL">
    </legend>

    <legend>
        <label for="UF_WORK_POSITION">Должность</label>
        <input class="form-target" value="<?=$arResult['USER_DATA']['WORK_POSITION']?>" type="text" id="UF_WORK_POSITION">
    </legend>

    <legend>
        <label for="UF_ADRESS">Адрес</label>
        <select class="form-target" id="UF_ADRESS">
            <option></option>
            <?foreach ($arResult['BCS'] as $bc):?>
                <option value="<?=$bc['ID']?>"><?=$bc['UF_NAME']?></option>
            <?endforeach;?>
        </select>
    </legend>

    <!--legend>
        <label for="fio">Тема</label>
        <select class="form-target" id="type">
            <option></option>
            <?foreach ($arResult['PROBLEM_CATEGORIES'] as $problem):?>
                <option value="<?=$problem['ID']?>"><?=$problem['UF_NAME']?></option>
            <?endforeach;?>
        </select>
    </legend-->

    <legend>
        <label for="NAME">Тема</label>
        <input required class="form-target" value="" type="text" id="NAME">
    </legend>

    <legend>
        <label for="UF_DESCRIPTION">Описание</label>
        <textarea class="form-target" id="UF_DESCRIPTION"></textarea>
    </legend>
    <br>
    <br>
    <div class="ui-btn-container ui-btn-container-center">
        <div onclick="addRequest()" class="feed-btn">Отправить</div>
    </div>


</form>
