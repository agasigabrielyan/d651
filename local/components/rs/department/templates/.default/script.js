function editDepartment(){
    BX.SidePanel.Instance.open("/bitrix/admin/iblock_section_edit.php?IBLOCK_ID=1&ID=2", {

        animationDuration: 100,
        width: 1000,
        cacheable: false,
    });
}
