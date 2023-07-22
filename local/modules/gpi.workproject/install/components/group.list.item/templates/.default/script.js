BX.ready(function(){
    let selector = document.querySelector('.show-group-actions');

    if(selector != null) {
        selector.addEventListener('click', function (event){
            let el = document.querySelector('.show-group-actions');

            let menu  = BX.PopupMenu.show("show-group-actions", el,
                [
                    {
                        text : "Участники",
                        className : "menu-popup-no-icon",
                        onclick: function(e, item){
                            BX.PreventDefault(e);
                            showInFrame(usersManageDir, 950);
                        }
                    },
                    {
                        text : "Редактировать",
                        className : "menu-popup-no-icon",
                        onclick: function(e, item){
                            BX.PreventDefault(e);
                            showInFrame('update/', 600);
                        }
                    },
                    {
                        text : "Удалить",
                        className : "menu-popup-no-icon",
                        onclick: function(e, item){
                            BX.PreventDefault(e);
                            showConfirmToGroupDelete();
                        }
                    },

                ],
                {
                    angle: false,
                    events: {
                    }
                });
        });
    }
});


function showConfirmToGroupDelete(){

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
                    id: projectId,
                },
            }).then(function (response) {
                location.href=workprojectsPath;

            }, function (response) {
                showNotyMessage("Произошла ошибка");
            });
        },
        modal: true,
    })

    box.show();

}