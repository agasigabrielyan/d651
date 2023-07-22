function loadEntity(){

    sendWorkForm(document.querySelector('.cool-form'), Array.from(document.querySelector('.cool-form').querySelectorAll('.field')), 'rs:tasks.list.item', 'loadEntity', 'BX.SidePanel.Instance.close();');
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
                let groups = event.getData().tag.getAvatar().split(',');
                if(inputHidden.getAttribute('name') == 'PROVIDER'){
                    let select = event.getTarget().getContainer().closest('form').querySelector('[name=GROUP_ID]');
                    select.innerHTML='';
                    for(let i in groups){
                        select.appendChild(BX.create({
                            tag : 'option',
                            text: projectGroups[groups[i]],
                            props: { value : groups[i]}
                        }))
                    }
                }

            },
            onTagRemove: function (event) {
                let inputHidden = event.getTarget().getContainer().closest('legend').querySelector('input');
                inputHidden.value = '';
                if(inputHidden.getAttribute('name') == 'PROVIDER'){
                    let select = event.getTarget().getContainer().closest('form').querySelector('[name=GROUP_ID]');
                    select.innerHTML='';
                }
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
    renderUser(document.querySelector('[name=PROVIDER]'));
    renderUser(document.querySelector('[name=PRODUCER]'));
})