<div class="entity-users-permission-render">

</div>

<script>
    BX.ready(function(){
        window.entityUserPermissionConfigurator = new entityUserPermissionConfigurator(document.querySelector('.entity-users-permission-render'), <?=Bitrix\Main\Web\Json::encode($arResult['LIST']??[])?>, <?=Bitrix\Main\Web\Json::encode($arParams)?>);
    })
</script>