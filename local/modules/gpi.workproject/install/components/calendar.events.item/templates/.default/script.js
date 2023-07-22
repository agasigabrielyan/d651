class RSCalendarItem{
    formShadow;
    popUp;
    selectedDate=new Date();
    dateDirection;
    activeCalendarDay;
    calendarView;
    container;
    slider;

    constructor(event) {
        this.container = document.querySelector('.element-form');

        this.slider = BX.SidePanel.Instance.getOpenSliders()[0];


        if(!event)
            event = this.slider.data.data;

        this.correctForm(event);
    }

    correctForm(data={}){
        let fileInputContainer
            ,fileShowLink
            ,fileList
            ,file
            ,self = this;

        for(let i in data){
            let tryFind = this.container.querySelector('[name='+i+']');
            if(!tryFind)
                continue;

            if(tryFind.getAttribute('name') == 'TYPE')
                tryFind.setAttribute('disabled', true);


            if(tryFind.getAttribute('type')!='file')
                tryFind.value = data[i];
            else
                new BearFileInput(tryFind);

        }

        let fileResnders = Array.from(this.container.querySelectorAll('input[type=file]'));
        for(let i in fileResnders){
            new BearFileInput(fileResnders[i]);
        }

        let userResnders = Array.from(this.container.querySelectorAll('[data-user-select-render]'));
        for(let i in userResnders){
            this.renderUserSelector(userResnders[i], userResnders[i].value);
        }

        if(BX.SidePanel.Instance.isOpen() == 12){
            this.container.querySelector('[name=TITLE]').closest('.legend').parentNode.classList.add('top-title');
            document.querySelector('.ui-side-panel-wrap-title-name').style.fontSize = 0+'px';
        }

        this.showEventTypeProps();

    }
    renderUserSelector(renderPlace, value){
        let arFields = {
            id: renderPlace.getAttribute('name'),
            multiple: false,
            dialogOptions: {
                context: 'MY_MODULE_CONTEXT',
                items: usersCalendarList,
                tabs:[ {
                    id: 'US_LIST',
                    title: 'Пользователи',
                    itemOrder: {title:'asc'},
                }]
            },
            events: {
                onAfterTagAdd: function (event) {
                    let userId = event.getData().tag.id;
                    let inputHidden = event.getTarget().getContainer().closest('.legend').querySelector('input');
                    inputHidden.value = userId;

                },
                onTagRemove: function (event) {
                    let inputHidden = event.getTarget().getContainer().closest('.legend').querySelector('input');
                    inputHidden.value = '';

                }
            }
        };
        if(parseInt(value)>0){
            arFields.items = [
                {
                    id : value,
                    title : usersCalendarList[usersCalendarList.findIndex(x=>x.id == value)].title,
                    entityId : 'userB',
                }
            ]
        }

        let tagSelector = new BX.UI.EntitySelector.TagSelector(arFields);
        renderPlace.hidden=true;

        tagSelector.renderTo(renderPlace.parentNode)

        window.tagger = tagSelector;
    }

    showNoty(text){
        BX.UI.Notification.Center.notify({
            content: text,
        });
    }

    loadData(){
        let formEls = Array.from(this.container.querySelectorAll('.field'))
            ,data = BX.ajax.prepareForm(this.container).data
            ,formData = {}
            ,self = this
            ,requiredMiss=[];


        for(let i in formEls){
            if(formEls[i].getAttribute('name') == null)
                continue;

            if(formEls[i].required && !formEls[i].value && !formEls[i].closest('.hidden'))
                requiredMiss.push(formEls[i]);
        }

        for(let i in requiredMiss){
            let container = requiredMiss[i].closest('legend');
            if(requiredMiss[i].getAttribute('name') == 'TITLE')
                container = requiredMiss[i].closest('.popup-window').querySelector('[name=TITLE]').closest('legend');

            if(container == null)
                continue;
            container.classList.add('error');

            container.querySelector('.field').addEventListener('change', function(event) {

                let target = event.target.closest('legend');

                if(target == null)
                    return true;

                target.classList.remove('error');
            })
        }

        if(requiredMiss.length>0){
            return false;
        }


        let filesArs = Array.from(this.container.querySelectorAll('.uploadFiles'));

        for(let i in filesArs){

            for(let index in filesArs[i].files)
            {
                data[filesArs[i].getAttribute('id')+index] = filesArs[i].files[index];
            }
        }

        let filesPostArs = Array.from(this.container.querySelectorAll('.inputFilesList__past  .inputFilesList__row'));
        data['FILES_POST_LIST'] = [];
        for(let i in filesPostArs){
            data[filesPostArs[i].dataset.name+'_POST_LIST'].push(filesPostArs[i].dataset.fileId);
        }

        const bxFormData = new BX.ajax.FormData();

        for(let name in data)
        {
            bxFormData.append(name, data[name]);
        }

        bxFormData.send(
            '/bitrix/services/main/ajax.php?mode=class&c=rs:calendar.events&action=loadEvent&sessid='+BX.bitrix_sessid(),
            function (response)
            {
                response = JSON.parse(response);

                if(response.status === 'success')
                {
                    BX.SidePanel.Instance.close();
                }
                else
                {
                    showNotyMessage("Ошибка сохранения")
                }
            },
            null,
            function(error)
            {
                //showNotyMessage("Ошибка сохранения2")
            }
        );
    }
    deleteEvent(id){
        if(userId == 0){
            this.showNoty('Необходима авторизация');
            return false;
        }

        let self = this;

        let box = new BX.UI.Dialogs.MessageBox({
            message: BX.create({
                tag : 'div',
                attrs : { style : 'font: 17px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
                html : 'Удалить событие?',
            }),
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
            okCaption: "ОК",
            onOk : function (messageBox){
                BX.ajax.runComponentAction('rs:calendar.events', 'deleteEvent', {
                    mode: 'class',
                    data: {id: id},
                }).then(function (response) {
                    BX.SidePanel.Instance.close();
                }, function (response) {
                    console.log(response);
                });
            },
            modal: true,
        })

        box.show();
    }
    
    showEventTypeProps(){
        let typeInput = this.container.querySelector('select[name=TYPE]');
        let toggleProps = Array.from(this.container.querySelectorAll('[data-event-toggle-prop]'));
        let constProps = Array.from(this.container.querySelectorAll('.Celement-form>.row>div:not([data-event-toggle-prop])'));
        let targets;
        let actualTarget;

        if(!typeInput)
            return;

        actualTarget = typeInput.value;
        for (let i in constProps){
            constProps[i].classList.add('hidden');
            setTimeout(() => {
                constProps[i].classList.remove('hidden');
            }, "300");

        }

        for(let i in toggleProps){
            targets = toggleProps[i].getAttribute('data-target').split(',');
            if(targets.findIndex(x => x == actualTarget) >= 0)
                toggleProps[i].classList.remove('hidden');
            else
                toggleProps[i].classList.add('hidden');
        }
    }
}