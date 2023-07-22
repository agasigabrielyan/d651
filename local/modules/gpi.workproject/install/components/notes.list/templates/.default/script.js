BX.ready(function(){
    BX.addCustomEvent('BX.Main.Filter:customEntityFocus', onFieldFocus);
    BX.addCustomEvent('BX.Main.Filter:apply', refreshTasksListContent);
})

function onFieldFocus(Field) {
    if(!Field.field.parentNode.querySelector('.ui-tag-selector-outer-container')){
        renderUser(Field.field, Field, Field.getCurrentValues());
    }
}

function renderUser(renderPlace, Field, user){
    let arFields = {
        id: renderPlace.getAttribute('name'),
        multiple: false,
        dialogOptions: {
            context: 'MY_MODULE_CONTEXT',
            items: projectUsersList,
            tabs:[ {
                id: 'US_LIST',
                title: 'Пользователи',
                itemOrder: {title:'asc'},
            }]
        },
        events: {
            onAfterTagAdd: function (event) {
                let userId = event.getData().tag.id;

                let title = projectUsersList[projectUsersList.findIndex(x=>x.id == userId)].title;
                Field.setSingleData(title, userId)

            },
            onTagRemove: function (event) {
                Field.setSingleData('', '')
            }
        }
    };
    if(user.value){
        arFields.items = [
            {
                id : user.value,
                title : user.label,
                entityId : 'userB',
            }
        ]
    }

    let tagSelector = new BX.UI.EntitySelector.TagSelector(arFields);
    tagSelector.renderTo(renderPlace.parentNode);
}

function refreshTasksListContent(){
    BX.ajax.runComponentAction('rs:tasks.list', 'getComponentTemplateResult', {
        mode: 'class',
        data: {
            params: tasksListParams,
        },
    }).then(function (response) {
        let parser = new DOMParser();
        let doc = parser.parseFromString(response.data, 'text/html');
        document.querySelector('.task-list').innerHTML = doc.querySelector('.task-list').innerHTML;
    }, function (response) {
        showNotyMessage("Произошла ошибка");
    });
}

function showConfirmToDeleteTask(taskId){

    let box = new BX.UI.Dialogs.MessageBox({
        message: BX.create({
            tag : 'div',
            attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
            html : 'Подтвердите удаление задачи',
        }),
        buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
        okCaption: "ОК",
        onOk: function(messageBox)
        {
            BX.ajax.runComponentAction('rs:tasks.list', 'deleteEntity', {
                mode: 'class',
                data: {
                    id: taskId,
                },
            }).then(function (response) {
                messageBox.close();
                refreshTasksListContent();
            }, function (response) {
                showNotyMessage("Произошла ошибка");
            });
        },
        modal: true,
    })

    box.show();

}