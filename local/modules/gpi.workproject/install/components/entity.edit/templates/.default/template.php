<form class="form entity-edit-form">

    <?foreach ($arParams['COLUMNS'] as $column):?>

        <?=$column['HTML']?>

    <?endforeach;?>

    <div class="ui-btn ui-btn-primary" onclick="loadForm()">Сохранить</div>
</form>