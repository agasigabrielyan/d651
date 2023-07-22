<?php
    $department = $arResult['DEPARTMENT'];
?>
<div class="department-card">
    <div class="department-title"><?=$department['NAME']?> <button class="ui-btn ui-btn-icon-edit ui-btn-primary" onclick="editDepartment()"></button></div>
    <div class="department-description"><?=$department['D_DETAIL_TEXT']?></div>

    <div class="department-info">
        <span>Руководитель:</span> Имя руководителя департамента
    </div>

    <div class="department-info">
        <span>Управление 1:</span> Имя руководителя управления 1
    </div>

    <div class="department-info">
        <span>Управление 2:</span> Имя руководителя управления 2
    </div>

    <!-- Можете продолжить добавлять другие управления и их руководителей по аналогии -->

</div>