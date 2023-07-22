class RSCalendar{
    formShadow;
    popUp;
    selectedDate=new Date();
    dateDirection;
    activeCalendarDay;
    calendarView;
    calendarName;
    permissions;

    constructor(formShadow, activeCalendarDayArr, calendarView) {
        this.formShadow = formShadow;
        this.activeCalendarDay = activeCalendarDayArr;
        this.calendarView = calendarView;
        this.showInFrame = 1;
        this.calendarName = calendarName;
        this.permissions = calendarParams.USER_PERMISSIONS;

        let self = this;
        document.querySelector('#scheduler_here').onclick = function(evt) {
            self.onClick(evt);
        }
        document.querySelector('#scheduler_here').ondblclick = function(evt) {
            self.onDbclick(evt);
        }

        if(activeCalendarList == 'Y'){

            if(document.querySelector('.shaduler-here-container').classList.contains('col-12')){
                this.showShadulerDayList();
            }else
                this.onDateClick(this.activeCalendarDay);

        }
    }

    refreshEvents(){
        let self = this;

        BX.ajax.runComponentAction('rs:calendar.events', 'getEvents', {
            mode: 'class',
            data: {
                arParams: calendarParams,
                month : BX.getCookie('calendarMonth'),
                year: BX.getCookie('calendarYear')
            },
        }).then(function (response) {

            scheduler.clearAll();
            scheduler.parse(response.data);
            self.onDateClick(self.selectedDate);

        }, function (response) {
            for(let i in response.errors){
                self.showNoty(response.errors[i].message);
            }
        });
    }

    getFormContentHtml(data={}){
        let formContent = BX.create("div", {
            children: [
                BX.create("form", {
                    html: this.formShadow.innerHTML,
                    'props': {
                        'className': 'calendar-popUp-form',
                    }
                })
            ]
        })
        ,fileInputContainer
        ,fileShowLink
        ,fileList
        ,file
        ,self = this

        for(let i in data){
            let tryFind = formContent.querySelector('[name='+i+']');
            if(!tryFind || !data[i])
                continue;

            if(tryFind.getAttribute('name') == 'TYPE')
                tryFind.setAttribute('disabled', true);


            if(tryFind.getAttribute('type')!='file')
                tryFind.value = data[i];
            else{
                new BearFileInput(tryFind);
            }
        }

        return formContent;
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

    showPopUp(props, id){

        if(!this.checkPermission(props.extendedProps))
            return false;

        if(!props.STARTED){
            let swapDate = new Date(this.selectedDate.getTime());
            props.STARTED = swapDate.toLocaleString().replaceAll(',', '');
            swapDate.setHours(23);
            swapDate.setMinutes(59);
            props.ENDED = swapDate.toLocaleString().replaceAll(',', '');
        }

        if(this.showInFrame == 1){

            if(!props.extendedProps)
                props.extendedProps = {ID: 0};
            let link = props.extendedProps.ID+'/';

            openSidePanel(link, 400, 'window.calendarRS.refreshEvents()', props);
            return;
        }

        let self = this;
        let confirmText;

        if(this.popUp){
            this.popUp.close();
            this.popUp = null;
        }




        if(props.ID)
            confirmText = 'Сохранить';
        else
            confirmText = 'Создать';

        let buttons = [
            new BX.PopupWindowButton({
                text: confirmText,
                className: "popup-window-button-accept",
                events: {
                    click: function () {
                        self.loadData(this,props, id);
                    }
                }
            }),
            new BX.PopupWindowButton({
                text: "Отмена",
                className: "popup-window-button-cancel",
                events: {
                    click: function () {
                        if(!self.popUp.popupContainer.querySelector('[name=ID]').value)
                            scheduler.deleteEvent(id);

                        this.popupWindow.close();
                    }
                }
            })
        ];

        if(props.ID)
            buttons.push(new BX.PopupWindowButton({
                text: 'Удалить',
                className: "popup-window-button-decline",
                events: {
                    click: function () {
                        self.deleteEvent(self.popUp.popupContainer.querySelector('[name=ID]').value);
                    }
                }
            }))

        self.popUp = new BX.PopupWindow(false, null, {
            content: self.getFormContentHtml(props),
            autoHide: true,
            closeIcon: {
                right: "0px",
                top: "0px"
            },
            titleBar: {
                content: BX.create("legend", {
                    html: '<input class="field" placeholder="Введите название темы.." value="'+props.TITLE+'" name="TITLE" type="text">',
                    props: {
                        'className': 'calendar-popUp-title',
                    },
                    events: {
                        change: function(evt){
                            evt.target.closest('.popup-window').querySelector('form').querySelector('[name=TITLE]').value = evt.target.value;
                        }
                    }
                })
            },
            zIndex: 0,
            offsetLeft: 0,
            offsetTop: 0,
            overlay: {
                backgroundColor: '#a6a6a6',
                position: 'fixed'
            },
            buttons: buttons,
            events: {
                onPopupClose: function() {
                    if(!self.popUp.popupContainer.querySelector('[name=ID]').value)
                        scheduler.deleteEvent(id);
                    self.popUp = null;
                }
            }
        });

        self.popUp.show();

        self.popUp.contentContainer.onclick = function(evt) {
            self.onClick(evt);
        }
        self.popUp.contentContainer.ondblclick = function(evt) {
            self.onDbclick(evt);
        }

        let userResnders = Array.from(self.popUp.contentContainer.querySelectorAll('[data-user-select-render]'));

        for(let i in userResnders){
            this.renderUserSelector(userResnders[i], userResnders[i].value);
        }

        this.showEventTypeProps();
    }
    showCalendarNameSetter(){
        let self = this;
        let popUp = new BX.PopupWindow(false, null, {
            content:    '<input  class="calendar-pop-input" value="'+this.calendarName+'" name="TITLE">',
            autoHide: true,
            closeIcon: {
                right: "0px",
                top: "0px"
            },
            titleBar: {
                content: BX.create("div", {
                    html: 'Редактирование имени календаря',
                    props: {
                        'className': 'calendar-pop-title',
                    },
                })
            },
            zIndex: 0,
            offsetLeft: 0,
            offsetTop: 0,
            overlay: {
                backgroundColor: '#a6a6a6',
                position: 'fixed'
            },
            buttons: [
                new BX.PopupWindowButton({
                    text: 'Сохранить',
                    className: "popup-window-button-accept",
                    events: {
                        click: function () {
                            self.saveCalendarName(this.popupWindow);
                        }
                    }
                }),
                new BX.PopupWindowButton({
                    text: "Отмена",
                    className: "popup-window-button-cancel",
                    events: {
                        click: function () {
                            this.popupWindow.close();
                        }
                    }
                })
            ],
        });

        popUp.show();
    }
    showNoty(text){
        BX.UI.Notification.Center.notify({
            content: text,
        });
    }

    saveCalendarName(popUp){
        let calendarNameInput = popUp.popupContainer.querySelector('input');

        if(popUp.value=='')
            return true;

        BX.ajax.runComponentAction('rs:calendar.events', 'setCalendarName', {
            mode: 'class',
            data: {
                name: popUp.value,
                entityTable: calendarEntityTable,
                entityId: calendarEntityId,
            },
        }).then(function (response) {
            if(response.errors){
                for(let i in response.errors){
                    self.showNoty(response.errors[i].message);
                }
            }else{
                this.calendarName = popUp.value;
                popUp.close();
            }
        }, function (response) {
            console.log(response);
        });
    }
    hiddenSave(props){
        this.popUp = {popupContainer : this.getFormContentHtml(props)};

        this.loadData();
    }
    loadData(popUp,props, id){
        let formEls = Array.from(this.popUp.popupContainer.querySelectorAll('.field'))
            ,data = BX.ajax.prepareForm(this.popUp.popupContainer.querySelector('form')).data
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


        let filesArs = Array.from(this.popUp.popupContainer.querySelectorAll('.uploadFiles'));

        for(let i in filesArs){

            for(let index in filesArs[i].files)
            {
                data[filesArs[i].getAttribute('id')+index] = filesArs[i].files[index];
            }
        }

        let filesPostArs = Array.from(this.popUp.popupContainer.querySelectorAll('.inputFilesList__past  .inputFilesList__row'));
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
                    if(popUp)
                        popUp.popupWindow.close();
                    this.popUp=null;
                    self.refreshEvents();
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
                    data: {id: id, arParams: calendarParams,},
                }).then(function (response) {
                    self.refreshEvents();
                }, function (response) {
                    console.log(response);
                });
            },
            modal: true,
        })

        box.show();
    }

    onDateClick(date){
        let self = this;
        let list = document.querySelector('.calendar-list');
        let pastSelectedDate = this.selectedDate;
        this.selectedDate = date;

        this.day = date.getDate();
        document.querySelector('#sheduler-day').value = date.getDate();
        BX.setCookie('activeCalendarDay',  date.toLocaleString().slice(0, 10), {expires: 86400});
        BX.setCookie('calendarDay', date.getDate(), {expires: 86400});

        let month = parseInt(date.getMonth()+1);
        if(month<10)
            month = '0'+month;
        document.querySelector('#sheduler-month').value = month;
        this.month = month;
        BX.setCookie('calendarMonth', month, {expires: 86400});

        let dateEnd = new Date(date.getTime());
        dateEnd.setDate(dateEnd.getDate() +  1);

        let events = scheduler.getEvents().filter(function(v){

            if(v.start_date<=date && v.end_date>dateEnd)
                return true;

            if(v.start_date>=date && v.end_date<dateEnd)
                return true;

            if(v.start_date<=date && v.end_date>date)
                return true;

        })

        let rows=[];
        for(let i in events){
            let event = events[i].extendedProps;

            let row = BX.create("div", {
                props: {
                    className: 'table-row editor-container row',
                },
                children: [
                    this.createTableTd('col-8', event.TITLE, {
                        click : function(){
                            self.showPopUp(event);
                        }
                    }),
                    this.createTableTd('actions', '<div class="delete" onclick="window.calendarRS.deleteEvent('+event['ID']+')">x</div>'),
                    this.createTableTd('col-2', event.STARTED),
                    this.createTableTd('col-2', event.ENDED),
                    //this.createTableTd('col-2', event.FACT_DATE_END),
                    //this.createTableTd('col-2', usersCalendarList[usersCalendarList.findIndex(x => x.id == parseInt(event.GUARANTOR))].title),
                    //this.createTableTd('col-2', usersCalendarList[usersCalendarList.findIndex(x => x.id == parseInt(event.EXECUTOR))].title),
                ]
            });
            rows.push(row);
        }

        if(rows.length==0) {
            list.style.maxHeight = '0%';
            return;
        }else
            list.style.maxHeight='100%';

        let table = BX.create("div", {
            props: {
                className: 'ui-table',
            },
            children: rows
        });

        if(date>pastSelectedDate || pastSelectedDate == undefined) {
            list.querySelector('.left-data').innerHTML='';
            list.querySelector('.left-data').appendChild(table);
            list.style.left = '0px';
            this.dateDirection='.left-data';
        }else if(date.getTime()===pastSelectedDate.getTime()){
            list.querySelector('.center-data').innerHTML = '';
            list.querySelector('.center-data').appendChild(table);
            pastSelectedDate = date;
            return;
        }else {
            list.querySelector('.right-data').innerHTML='';
            list.querySelector('.right-data').appendChild(table);
            list.style.left = '-'+(2*list.parentNode.getBoundingClientRect().width)+'px';
            this.dateDirection='.right-data';
        }


        list.classList.add('no-anim');
        list.querySelector('.center-data').innerHTML = '';
        list.querySelector('.center-data').appendChild(table);
        list.style.left = '-'+list.parentNode.getBoundingClientRect().width+'px';
        list.classList.remove('no-anim');
    }
    onClick(evt){
        if(evt.target.classList.contains('dhx_month_head') || evt.target.classList.contains('dhx_month_body')){

           let date = new Date(Date.parse(evt.target.parentNode.getAttribute('data-cell-date')));

           if(document.querySelector('.shaduler-here-container').classList.contains('col-12')){
               this.showShadulerDayList();
               setTimeout(() => {
                   this.onDateClick(date);
               }, "500");
           }else
               this.onDateClick(date);


           BX.setCookie('calendarDate', evt.target.parentNode.getAttribute('data-cell-date').replaceAll(' 00:00', ''), {expires: 86400});
        }

        if(evt.target.classList.contains('dhx_scale_bar') && this.calendarView == 'week'){

           var selector = evt.target.parentNode.querySelectorAll('.dhx_scale_bar');

           for (var i = 0; i < selector.length; i++) {
               var element = selector[i];
               var child = element.parentNode.firstChild;
               var index = 0;

               while (true) {
                   if (child.nodeType === Node.ELEMENT_NODE) {
                       index++;
                   }

                   if (child === element || !child.nextSibling) {
                       break;
                   }

                   child = child.nextSibling;
               }

               element.setAttribute('number',index-1);
           }
           let date = new Date(scheduler._min_date.getTime());

           date.setDate(date.getDate() +  parseInt(evt.target.getAttribute('number')));
           this.onDateClick(date);

           BX.setCookie('activeCalendarDay', date.toLocaleString().slice(0, 10), {expires: 86400});
        }

        if(event.target.classList.contains('inputFilesList__title'))
        {
            if(event.target.closest('.inputFilesList'))
            {
                event.target.closest('.inputFilesList').style.display = 'none';
            }
        }
        if(event.target.classList.contains('inputFiles__link'))
        {
            event.preventDefault();
            event.target.parentNode.parentNode.parentNode.querySelector('.inputFilesList').style.display = 'block';
        }

        if(!event.target.classList.contains('remove')
            && !event.target.classList.contains('inputFiles__link')
            && !event.target.classList.contains('name')
            && !event.target.classList.contains('inputFiles__trigger')
        )
        {
            if(event.target.closest('.inputFilesList'))
            {
                event.target.closest('.inputFilesList').style.display = 'none';
            }
        }

        if(event.target.classList.contains('uploadFiles'))
        {
            this.checkFiles(event.target)
        }
    }
    onDbclick(evt){

        if(evt.target.closest('.dhx_cal_event') && this.calendarView == 'week'){

            //let evtId = evt.target.closest('.dhx_cal_event').getAttribute('event_id');
            //scheduler.showLightbox(evtId);
        }
    }
    onBeforeLightbox(id){

        return false;
    }
    onEventClick(id){
        let event = scheduler.getEvent(id);

        if(!event.extendedProps)
            event.extendedProps={TITLE:''};

        event.end_date = checkTime(event.end_date);

        event.extendedProps.STARTED = event.start_date.toLocaleString().replaceAll(',', '');
        event.extendedProps.ENDED = event.end_date.toLocaleString().replaceAll(',', '');

        if(!event.extendedProps.STARTED){
            let swapDate = new Date(this.selectedDate.getTime());
            event.extendedProps.STARTED = swapDate.toLocaleString().replaceAll(',', '');
            swapDate.setHours(23);
            swapDate.setMinutes(59);
            event.extendedProps.ENDED = swapDate.toLocaleString().replaceAll(',', '');
        }

        if(!this.checkPermission(event.extendedProps))
            return false;

        if(this.showInFrame == 1){
            if(!event.extendedProps)
                event.extendedProps = {ID: 0};
            let link = event.extendedProps.ID+'/';

            openSidePanel(link, 400, 'window.calendarRS.refreshEvents()', event.extendedProps);
        }else{
            this.showPopUp(event.extendedProps, id, event.extendedProps);
        }
    }
    onEventCreated(id){
        let event = scheduler.getEvent(id);

        if(!event.extendedProps)
            event.extendedProps={TITLE:''};

        event.end_date = checkTime(event.end_date);

        //event.extendedProps.STARTED = event.start_date.toLocaleString().replaceAll(',', '');
        //event.extendedProps.ENDED = event.end_date.toLocaleString().replaceAll(',', '');

        this.showPopUp(event.extendedProps, id);
    }
    onEventChanged(id){
        let event = scheduler.getEvent(id);

        event.end_date = checkTime(event.end_date);

        event.extendedProps.STARTED = event.start_date.toLocaleString().replaceAll(',', '');
        event.extendedProps.ENDED = event.end_date.toLocaleString().replaceAll(',', '');

        this.hiddenSave(event.extendedProps);
    }
    onViewChange(mode,date){
        this.selectedDate = date;
        this.onDateClick(date);

        if(mode == 'week'){
            let anHourHeight = document.querySelector('.dhx_scale_hour').getBoundingClientRect().height;
            let eventsContainer = document.querySelector('.dhx_cal_data');
            eventsContainer.scrollTop=anHourHeight*9;
        }
        this.refreshEvents();
        return true
    }
    onCreateToolTip(start,end,event){
        let info=[];
        let eventExtendedProps = event.extendedProps;

        info.push('<b>Тема:</b> '+event.text);
        info.push('<b>Дата начала:</b> '+start.toLocaleString());
        info.push('<b>Дата окончания:</b> '+end.toLocaleString());

        switch (eventExtendedProps.TYPE) {
            case 'EVENT':
                if(eventExtendedProps.PROVIDER)
                    if(usersCalendarList.findIndex(x=>x.id == eventExtendedProps.PROVIDER)>=0)
                        info.push('<b>Организатор:</b> '+usersCalendarList[usersCalendarList.findIndex(x=>x.id == eventExtendedProps.PROVIDER)].title);
                break;

            case 'TASK':
                if(eventExtendedProps.GUARANTOR)
                    if(usersCalendarList.findIndex(x=>x.id == eventExtendedProps.GUARANTOR)>=0)
                        info.push('<b>Поручитель:</b> '+usersCalendarList[usersCalendarList.findIndex(x=>x.id == eventExtendedProps.GUARANTOR)].title);
                if(eventExtendedProps.EXECUTOR)
                    if(usersCalendarList.findIndex(x=>x.id == eventExtendedProps.EXECUTOR)>=0)
                        info.push('<b>Исполнитель:</b> '+usersCalendarList[usersCalendarList.findIndex(x=>x.id == eventExtendedProps.EXECUTOR)].title);

                break;

            case 'WORK':
                if(eventExtendedProps.EXECUTOR)
                    if(usersCalendarList.findIndex(x=>x.id == eventExtendedProps.EXECUTOR)>=0)
                        info.push('<b>Исполнитель:</b> '+usersCalendarList[usersCalendarList.findIndex(x=>x.id == eventExtendedProps.EXECUTOR)].title);

                if(eventExtendedProps.FACT_STARTED)
                    info.push('<b>Дата начала факт:</b> '+eventExtendedProps.FACT_STARTED);

                if(eventExtendedProps.FACT_ENDED)
                    info.push('<b>Дата окончания факт:</b> '+eventExtendedProps.FACT_ENDED);
                break;
        }

        if(eventExtendedProps.DESCRIPTION)
            info.push('<b>Описание:</b> '+eventExtendedProps.DESCRIPTION);


        return '<div onclick="window.calendarRS.onEventClick('+event.id+')" class="event-tool">'+info.join('</br>')+'</div>';
    }
    onHourBuild(date){
        let html="";
        let minute="";
        for (var i=0; i<60/30; i++){
            minute = date.getMinutes();
            if(minute===0)
                minute = '00';
            html+="<div style='height:30px;line-height:30px;'>"+date.getHours()+':'+minute+"</div>";
            date = scheduler.date.add(date,30,"minute");
        }
        return html;
    }


    createTableTd(adclass, value, events={}){
        return BX.create("div", {
            html: value,
            props: {
                className: 'table-col '+ adclass,
            },
            events: events
        });
    }

    showShadulerDayList(day){
        document.querySelector('.shaduler-here-container').classList.add('collapsed');
        document.querySelector('.shaduler-here-container').classList.remove('col-12');
        document.querySelector('.shaduler-here-container').classList.add('col-5');
        document.querySelector('.calendar-list-container').classList.add('collapsed');
        document.querySelector('.calendar-list-container').classList.remove('col-0');
        document.querySelector('.calendar-list-container').classList.add('col-7');
        BX.setCookie('activeCalendarList', 'Y', {expires: 86400});

        setTimeout(() => {
            document.querySelector('.shaduler-here-container').classList.remove('collapsed');
            document.querySelector('.calendar-list-container').classList.remove('collapsed');
        }, "400");
    }
    hideShadulerDayList(){

        document.querySelector('.shaduler-here-container').classList.add('collapsed');
        document.querySelector('.shaduler-here-container').classList.add('col-12');
        document.querySelector('.shaduler-here-container').classList.remove('col-5');
        document.querySelector('.calendar-list-container').classList.add('collapsed');
        document.querySelector('.calendar-list-container').classList.add('col-0');
        document.querySelector('.calendar-list-container').classList.remove('col-7');
        BX.setCookie('activeCalendarList', 'N', {expires: 86400});

        setTimeout(() => {
            document.querySelector('.shaduler-here-container').classList.remove('collapsed');
            document.querySelector('.calendar-list-container').classList.remove('collapsed');
        }, "400");

    }

    changeView(el){
        let view;
        if(el.checked)
            view='week';
        else
            view='month';

        this.calendarView = view;

        BX.setCookie('calendarView', view, {expires: 86400});
        let month = document.querySelector('#sheduler-month').value;
        let year = document.querySelector('#sheduler-year').value;
        let dayS = document.querySelector('#sheduler-day').value;
        scheduler.setCurrentView(new Date(year, month-1, dayS), view);
    }

    setShedulerDate(){
        let dayS = document.querySelector('#sheduler-day').value;
        let month = document.querySelector('#sheduler-month').value;
        let year = document.querySelector('#sheduler-year').value;


        BX.setCookie('calendarYear', year, {expires: 86400});
        BX.setCookie('calendarMonth', month, {expires: 86400});
        BX.setCookie('calendarDay', dayS, {expires: 86400});
        this.year = year;
        this.month = month;
        this.day = dayS;


        scheduler.setCurrentView(new Date(year, month-1, dayS));
    }

    checkFiles(el){
        let thisInput = el.closest('.inputFiles').querySelector('input');

        if(thisInput)
        {

            let filesCount = 0;
            let filesShowLink = thisInput.parentNode.querySelector('.inputFiles__link');
            let filesListDiv = thisInput.parentNode.parentNode.querySelector('.inputFilesList');


            let filesContainerDiv = thisInput.parentNode.parentNode.querySelector('.inputFilesList__list');
            filesContainerDiv.innerHTML = '';

            thisInput.addEventListener('change', (event)=>{

                let files = Array.from(thisInput.files);

                filesCount = files.length;

                if(filesCount>=1)
                {
                    filesShowLink.style.display = 'block';
                }
                let newBlock = true;
                let column;
                let counter = 0;

                if(!event.target.getAttribute('multiple')){
                    let filesListColumns = Array.from(filesContainerDiv.querySelectorAll('.inputFilesList__column'));
                    for(let i in filesListColumns){
                        filesListColumns[i].remove();
                    }
                }


                for(let i =0; i<files.length; i++)
                {
                    if(newBlock == true)
                    {
                        column = document.createElement('div');
                        column.classList.add('inputFilesList__column');
                        column.innerHTML = '';
                        newBlock = false;
                    }

                    let thisFile = files[i];
                    let filename = files[i].name;


                    let row = document.createElement('div');
                    row.classList.add('inputFilesList__row');

                    let name = document.createElement('div');
                    name.classList.add('name');
                    name.innerHTML = filename;

                    let remove = document.createElement('button');
                    remove.classList.add('remove');

                    remove.addEventListener('click',(event)=>{
                        event.preventDefault();
                        files.splice(files.indexOf(thisFile),1);
                        row.remove();
                        if(!files.length)
                        {
                            filesShowLink.style.display = 'none';
                            filesListDiv.style.display = 'none';
                        }
                    });

                    row.append(name);
                    row.append(remove);

                    column.append(row);

                    counter++;
                    if(counter % 7 == 0 || counter>=filesCount)
                    {
                        //пушим блок
                        newBlock = true;
                        filesContainerDiv.append(column);
                    }
                }
            });
        }
    }
    showEventTypeProps(){
        let typeInput = this.popUp.contentContainer.querySelector('select[name=TYPE]');
        let toggleProps = Array.from(this.popUp.contentContainer.querySelectorAll('[data-event-toggle-prop]'));
        let constProps = Array.from(this.popUp.contentContainer.querySelectorAll('.Ccalendar-popUp-form>.row>div:not([data-event-toggle-prop])'));
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
    checkPermission(props = []){
        let X_USER = this.permissions.findIndex(x => x == 'X'),
            W_USER = this.permissions.findIndex(x => x == 'W');

        if(X_USER || (!props.ID && W_USER) || (props.ID && W_USER && props.CREATED_BY == userId))
            return true;

        return false;
    }
}