function saveConfig(){

    let project = document.querySelector('[name=PROJECT_ID]'),
        director = document.querySelector('[name=DIRECTOR_ID]'),
        titleInput = document.querySelector('[name=TITLE]');

    if(!project.value){
        showNotyMessage('Неопознанный проект');
        return;
    }

    if(!titleInput.value){
        showNotyMessage('Введите название проекта');
        return;
    }

    if(!director.value){
        showNotyMessage('Введите руководителя проекта');
        return;
    }

    BX.ajax.runComponentAction('rs:project.list.item', 'renameProject', {
        mode: 'class',
        data: {
            id: project.value,
            title : titleInput.value,
            director : director.value,
        },
    }).then(function (response) {
        window.entityUserPermissionConfigurator.save();
    }, function (response) {
        console.log(response);
    });
}

function renderUser(renderPlace){
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
                let inputHidden = event.getTarget().getContainer().closest('legend').querySelector('input');
                inputHidden.value = userId;

            },
            onTagRemove: function (event) {
                let inputHidden = event.getTarget().getContainer().closest('legend').querySelector('input');
                inputHidden.value = '';

            }
        }
    };
    if(parseInt(renderPlace.value)>0){
        arFields.items = [
            {
                id : renderPlace.value,
                title : projectUsersList[projectUsersList.findIndex(x=>x.id == renderPlace.value)].title,
                entityId : 'userB',
            }
        ]
    }

    let tagSelector = new BX.UI.EntitySelector.TagSelector(arFields);
    renderPlace.hidden=true;

    tagSelector.renderTo(renderPlace.parentNode)
}

BX.ready(function(){
    renderUser(document.querySelector('[name=DIRECTOR_ID]'));
})