function refreshGroupsContent(el){

    BX.ajax.runComponentAction('rs:group.list', 'getComponentTemplateResult', {
        mode: 'class',
        data: {
            params: window.groupListComponentParams,
        },
    }).then(function (response) {
        let parser = new DOMParser();
        let doc = parser.parseFromString(response.data, 'text/html');
        document.querySelector('.group-list').innerHTML = doc.querySelector('.group-list').innerHTML;
    }, function (response) {
        showNotyMessage("Произошла ошибка");
    });
}

function showConfirmToGroupDelete(groupId, el){

    let box = new BX.UI.Dialogs.MessageBox({
        message: BX.create({
            tag : 'div',
            attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
            html : 'Подтвердите удаление группы',
        }),
        buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
        okCaption: "ОК",
        onOk: function(messageBox)
        {
            BX.ajax.runComponentAction('rs:group.list.item', 'deleteEntity', {
                mode: 'class',
                data: {
                    id: groupId,
                },
            }).then(function (response) {
				messageBox.close();
                refreshProjectEntityStructureContent();
                refreshGroupsContent();
            }, function (response) {
                showNotyMessage("Произошла ошибка");
            });
        },
        modal: true,
    })

    box.show();

}

BX.ready(function(){
    let disabledLinks = Array.from(document.querySelectorAll('.table-data.disabled'));

    for (let i in disabledLinks){
        BX.UI.Hint.init(disabledLinks[i]);
    }
})

function showConfirmToUserDelete(userId){

    let box = new BX.UI.Dialogs.MessageBox({
        message: BX.create({
            tag : 'div',
            attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
            html : 'Подтвердите удаление пользователя',
        }),
        buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
        okCaption: "ОК",
        onOk: function(messageBox)
        {
            BX.ajax.runComponentAction('rs:workprojects.project.users.edit', 'deleteUsers', {
                mode: 'class',
                data: {
                    delete : [{ID : userId}],
                }
            }).then(function (response) {
                messageBox.close();
				refreshGroupsContent();
            });
        },
        modal: true,
    })

    box.show();
}