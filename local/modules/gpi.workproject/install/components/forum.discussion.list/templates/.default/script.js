function refreshThemesContent(){
    BX.ajax.runComponentAction('rs:forum.discussion.list', 'getComponentTemplateResult', {
        mode: 'class',
        data: {
            params: window.forumListComponentParams,
        },
    }).then(function (response) {
        let parser = new DOMParser();
        let doc = parser.parseFromString(response.data, 'text/html');
        document.querySelector('.themes-list').innerHTML = doc.querySelector('.themes-list').innerHTML;
    }, function (response) {
        showNotyMessage("Произошла ошибка");
    });
}


function showConfirmToDeleteTheme(id){

    let box = new BX.UI.Dialogs.MessageBox({
        message: BX.create({
            tag : 'div',
            attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
            html : 'Подтвердите удаление темы',
        }),
        buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
        okCaption: "ОК",
        onOk: function(messageBox)
        {
            BX.ajax.runComponentAction('rs:workprojects.forum', 'deleteEntity', {
                mode: 'class',
                data: {
                    id: id,
                },
            }).then(function (response) {
                messageBox.close();
                refreshThemesContent();

            }, function (response) {
                showNotyMessage("Произошла ошибка");
            });

        },
        modal: true,
    })

    box.show();

}


