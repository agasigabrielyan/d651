function editDepartment(){
    BX.SidePanel.Instance.open("/bitrix/services/main/ajax.php?mode=class&c=rs:department&action=getEditComponentResult", {
        animationDuration: 100,
        width: 700,
        cacheable: false,
    });
}
