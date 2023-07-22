class toolPicker {

    id;
    placement;
    toolLine;
    btnsCont;
    toolBox={};
    btnText;
    config;
    view;
    items=[];
    btns=[];

    constructor(config, placement, btnText="Создать", view = 'flex', btns = [], id='', title) {

        if(!placement)
            return false;

        if(id == '')
            id = 'tool_'+parseInt(Math.random() * 999999999);

        this.placement = placement;
        this.config = config;
        this.view = view;
        this.btns = btns;
        this.btnText = btnText;
        this.id = id;
        this.title = title;


        let self = this;
        let clickAction = function(e) {self.clickListener(event);};

        document.body.addEventListener('click', BX.proxy(clickAction, this), false);

        this.constractView();

        if(view == 'table'){
            var styleSheet = document.createElement("style")
            styleSheet.innerText = '.tool-items-cont.'+id+' td:nth-child('+this.config.length+'):before{display: none}'
            document.head.appendChild(styleSheet)
        }
    }


    /*
     * Dom
     */
    constractView(){

        let tool = this;
        let addAction = function(){
            tool.createToolBox();
        };
        let pressAction = function(){
            tool.eventPress(event);
        };

        let buttons = [];

        buttons.push(
            BX.create({
                tag: 'div',
                props: {className: 'ui-btn-sm ui-btn-primary ui-btn-icon-add'},
                text: this.btnText,
                events:{
                    click : BX.proxy(addAction, this)
                }
            })
        );

        for(let i in this.btns){
            let btn = this.btns[i];
            buttons.push(
                BX.create({
                    tag: 'div',
                    props: {className: 'ui-btn-sm ui-btn-primary'},
                    text: btn.title,
                    events:{
                        click : BX.proxy(btn.function, this)
                    }
                })
            )
        }


        document.body.addEventListener('keypress', BX.proxy(pressAction, this));

        let title

        if(this.title){
            title = BX.create({
                    tag : 'div',
                    attrs: {className : 'tool-box-title'},
                    text : this.title,
            });
        }

        let toolLine ;

        switch (this.view) {
            case 'flex':
                toolLine = BX.create({
                    tag : 'div',
                    props: {className: 'tool-items-cont '+this.id},
                    children: [
                        title,
                        BX.create({
                            tag : 'div',
                            props: {className: 'tool-items-line'},
                        }),
                        BX.create({
                            tag: 'div',
                            props: {className: 'tool-box-actions'},
                            children: buttons,
                        })

                    ]
                });
                break;

            case 'table':
                let tableHeadObjects = [];
                for(let i=0; i< this.config.length; i++){
                    let item = this.config[i];

                    tableHeadObjects.push(BX.create({
                        tag: 'th',
                        text : item.title,
                    }))
                }

                toolLine = BX.create({
                    tag : 'div',
                    props: {className: 'tool-items-cont '+this.id},
                    children: [
                        title,
                        BX.create({
                            tag : 'table',
                            props: {className: 'tool-items-table'},
                            children : [
                                BX.create({
                                    tag: 'thead',
                                    attrs : {className : 'items-table-head'},
                                    children:[
                                        BX.create({
                                            tag: 'tr',
                                            children : tableHeadObjects
                                        })
                                    ]
                                }),
                                BX.create({
                                    tag: 'tbody',
                                    attrs: {className: 'tool-items-line'},
                                })
                            ]
                        }),
                        BX.create({
                            tag: 'div',
                            props: {className: 'tool-box-actions'},
                            children: buttons,
                        })
                    ]
                });
                break;

        }

        this.placement.innerHTML = '';
        this.placement.appendChild(toolLine);
        this.toolLine = this.placement.querySelector('.tool-items-line');
        this.btnsCont = this.placement.querySelector('.tool-box-actions');
    }

    dropActions(){

        if(this.btnsCont){
            this.btnsCont.innerHTML = '';
        }
    }

    loadItems(items){

        for (let index in items){
            let id = this.items.length+1;
            let data = items[index];

            let edited = data.edited;
            delete data.edited;

            let externalId = data.externalId;
            delete data.externalId;

            this.createToolItem(id, data, edited);

            data.id = id;
            data.externalId = externalId;

            this.items.push(data);
            this.onAfterLoad(id, index);
        }
    }


    createToolBox(){
        let tool = this;
        this.toolBox.mode = 'create';

        let toolQuestsions = BX.create({
            tag : 'div',
            props : { className : 'tool-questions-line'},
        })

        for(let i=0; i< this.config.length; i++){
            let item = this.config[i];
            switch (item.type){

                case 'select' :
                    toolQuestsions.appendChild(this.createSelectQst(item));
                    break;

                case 'js-life-select' :
                    toolQuestsions.appendChild(this.createJSLifeSelectQst(item));
                    break;

                case 'life-select' :
                    toolQuestsions.appendChild(this.createLifeSelectQst(item));
                    break;

                case 'int' :
                    toolQuestsions.appendChild(this.createIntQst(item));
                    break;

                case 'text' :
                    toolQuestsions.appendChild(this.createTextQst(item));
                    break;

                case 'checkbox' :
                    toolQuestsions.appendChild(this.createCheckboxQst(item));
                    break;
            }
        }

        let saveBtnClickEvnt = function(){
            tool.startSaving();
        };


        let toolBox = new BX.UI.Dialogs.MessageBox({
            message: BX.create({
                tag : 'form',
                props : {className:'tool-questions '+this.id},
                children : [
                    toolQuestsions
                ],
            }),
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
            okCaption: "Сохранить",
            onOk: BX.proxy(saveBtnClickEvnt, this),
        })
        toolBox.show();
        this.toolBox.document = toolBox;
        this.onCreateToolBox(toolBox);
    }

    createUpdateToolBox(id){
        this.toolBox.mode = 'update';
        this.toolBox.selected = id;
        if(document.querySelector('.tool-select-box'))
            document.querySelector('.tool-select-box').remove();

        let tool = this;

        let toolQuestsions = BX.create({
            tag : 'div',
            props : { className : 'tool-questions-line'},
        })

        let itemData = this.items.find(x => x.id === id);

        if(!itemData)
            itemData = new Object();

        for(let i in this.config){
            let item = this.config[i];

            switch (item.type){

                case 'select' :
                    toolQuestsions.appendChild(this.createSelectQst(item,itemData[item.code]));
                    break;

                case 'js-life-select' :
                    toolQuestsions.appendChild(this.createJSLifeSelectQst(item,itemData[item.code]));
                    break;

                case 'life-select' :
                    toolQuestsions.appendChild(this.createLifeSelectQst(item,itemData[item.code]));
                    break;

                case 'int' :
                    toolQuestsions.appendChild(this.createIntQst(item,itemData[item.code]));
                    break;

                case 'text' :
                    toolQuestsions.appendChild(this.createTextQst(item,itemData[item.code]));
                    break;

                case 'checkbox' :
                    toolQuestsions.appendChild(this.createCheckboxQst(item, itemData[item.code]));
                    break;
            }
        }

        let closeBtnClickEvnt = function(){
            tool.dropToolBox();
        };

        let updateBtnClickEvnt = function(){
            tool.startSaving(id);
        };

        let toolBox = new BX.UI.Dialogs.MessageBox({
            message: BX.create({
                tag : 'form',
                props : {className:'tool-questions '+this.id},
                children : [
                    toolQuestsions
                ],
            }),
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
            okCaption: "Сохранить",
            onOk: BX.proxy(updateBtnClickEvnt, this)
        })

        toolBox.show();
        this.toolBox.document = toolBox;
        this.onCreateToolBox(toolBox);
    }

    reloadToolBox(id){
        let Boxid = this.toolBox.selected;

        this.dropToolBox();

        if(this.toolBox.mode == 'update')
            this.correctToolItem(Boxid);
        else
            this.createToolBox();
    }

    dropToolBox(){
        if(this.toolBox.document){
            this.toolBox.document.close();
            this.toolBox.selected = false;
            this.toolBox.document=null;
        }
    }


    createToolItem(id, data, edited = true){
        let itemBodyList = [];

        let itemData = new Object();

        for(name in data){

            let value = data[name];

            let configItem = this.config.find(x => x.code === name);

            if(configItem == undefined)
                continue;

            if(configItem.type=='select' && configItem.multiple){
                let swap = value;
                value='';
                for(let i in swap){
                    let optionData = configItem.list[configItem.list.findIndex(x => x.id === swap[i])];
                    if(optionData.view)
                        value = value+ " " + optionData.view;
                    else
                        value = value+ " " + optionData.label;

                }
            }else if( configItem.list && configItem.type!='text') {
                value = configItem.list.find(x => x.id === value).label;
            }


            if(value === true)
                value='Да';

            else if(value === false)
                value='Нет';



            switch (this.view) {
                case 'flex':
                    itemBodyList.push(BX.create({
                        tag: 'div',
                        attrs: {className: 'tool-option'},
                        children: [
                            BX.create({
                                tag: 'div',
                                attrs: {className: 'label'},
                                text: configItem.title
                            }),
                            BX.create({
                                tag: 'div',
                                attrs: {className: 'delimetr'},
                                text: ': '
                            }),
                            BX.create({
                                tag: 'div',
                                attrs: {className: 'value', name : configItem.code},
                                html: value
                            }),
                        ],
                    }))
                    break;

                case 'table':
                    itemBodyList.push(BX.create({
                        tag: 'td',
                        html:value,
                        attrs : {name : configItem.code}
                    }))
                    break;
            }
        }

        let self = this;

        let correctAction = function(e) {self.createUpdateToolBox(id);};
        let removeAction = function(e) {self.removeToolItem(id);};

        let itemBody;

        let actionBody = BX.create({
            tag : 'div',
        });

        if(edited == true)
            actionBody = BX.create({
            tag : 'div',
            attrs : { className : 'actions-cont'},
            children : [
                BX.create({
                    tag : 'div',
                    text : '🪶',
                    attrs : {className: 'correct'},
                    events : {click : BX.proxy(correctAction, this)}
                }),
                BX.create({
                    tag : 'div',
                    text : '❌',
                    attrs : {className: 'remove'},
                    events : {click : BX.proxy(removeAction, this)}
                })
            ]
        });

        switch (this.view) {
            case 'flex':
                itemBodyList.push(actionBody);
                itemBody = BX.create({
                    tag : 'div',
                    attrs : {className : 'tool-item', item : 'tool_'+id},
                    children: itemBodyList
                });
                break;

            case 'table':
                itemBodyList.push(BX.create({
                    tag:'td',
                    attrs: {className : 'action-td-cont'},
                    children: [actionBody]
                }));
                itemBody = BX.create({
                    tag : 'tr',
                    attrs : {className : 'tool-item', item : 'tool_'+id},
                    children: itemBodyList
                });
                break;
        }

        this.toolLine.appendChild(itemBody);
        this.dropToolBox();
    }

    updateToolItem(id, data){

        let itemBody = this.toolLine.querySelector('.tool-item[item=tool_'+id+']');

        for (let name in data) {

            let value = data[name];
            let configItem = this.getConfigItem(name);

            if(configItem.type=='select' && configItem.multiple){
                let swap = value;
                value='';
                for(let i in swap){
                    let optionData = configItem.list[configItem.list.findIndex(x => x.id === swap[i])];
                    if(optionData.view)
                        value = value+ " " + optionData.view;
                    else
                        value = value+ " " + optionData.label;

                }
            }else if(configItem.list && configItem.type!='text') {
                value = configItem.list.find(x => x.id === value).label;
            }

            if(value === true)
                value='Да';

            else if(value === false)
                value='Нет';

            itemBody.querySelector('[name='+name+']').innerHTML = value;
        }

        this.dropToolBox();
    }

    removeToolItem(id){
        let tool = this;
        if(!this.onBeforeDelete(id))
            return false;

        const messageBox = new BX.UI.Dialogs.MessageBox({
            message: 'Подтверждаю безвозвратное удаление',
            title: "Удаление",
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
            okCaption: "Да",
            onOk: function() {
                let itemBody = tool.toolLine.querySelector('.tool-item[item=tool_'+id+']');



                itemBody.remove();

                tool.items = tool.items.filter(function( obj ) {
                    if(obj.id != id){
                        return 1;
                    }else{
                        tool.onAfterDelete(id, obj);
                    }

                    messageBox.close();

                });
            }
        })

        messageBox.show();

    }


    /*
     * Form read
     */
    startSaving(id = 0){

        let form = document.querySelector('.tool-questions')
        let items = Array.from(form.querySelectorAll('.tool-quest-item'));
        let data = {};
        for (let i in items) {
            let item = items[i];
            if(item){
                if(item.getAttribute('type') != 'select' && item.multiple) {
                    data[item.getAttribute('name')]=[];
                    Array.from(item.querySelectorAll('option:checked')).forEach(function(opt){
                        data[item.getAttribute('name')].push(opt.getAttribute('value'));
                    })
                }else if(item.getAttribute('type') != 'checkbox')
                    data[item.getAttribute('name')] = item.value;
                else
                    data[item.getAttribute('name')] = item.checked;
            }
        }
        data = this.onSave(data);

        if(data == false){
            let tool = this;
            let saveAction = function(){
                tool.startSaving(tool.toolBox.mode == 'create'? '' : tool.toolBox.selected);
            }
            this.toolBox.document.buttons[0].events.click = BX.proxy(saveAction, this);
            this.toolBox.document.buttons[0].button.removeAttribute('disabled');
            this.toolBox.document.buttons[0].disabled = false;
            this.toolBox.document.buttons[0].button.classList.remove('ui-btn-disabled');
            return false;
        }

        if(id != 0)
            this.update(id, data);
        else
            this.create(data);
    }


    /*
     * Line options
     */
    createLifeSelectQst(conf, value=''){
        let questionBody = this.createQuestionBody(conf);
        let tool = this;
        let changeAction = function(){
            tool.onInputEvent(event)
        }

        let prevalue = '';
        if(value && conf.list.find(x => x.id == value))
            prevalue = conf.list.find(x => x.id == value).label;

        let inputAction = function(){
            tool.LifeSelectOninput(event, conf);
        };

        let selectAction = function(){
            tool.jsLifeSelectOnSelect(event);
        };

        let select = BX.create({
            tag : 'select',
            props : { name : conf.code, 'hidden' : true, className: 'true-select tool-quest-item'},
            events:{
                change : BX.proxy(changeAction, this)
            }
        });

        let input = BX.create({
            tag : 'input',
            props : { className: 'select-head', type : 'text', value : prevalue},
            events : {
                input : BX.proxy(inputAction, this),
                click : BX.proxy(inputAction, this),
            }
        });

        let viewOptionsCont = BX.create({
            tag : 'div',
            props : { className: 'select-options'},
        });

        if(conf.list)
            conf.list.forEach(function(option){
            let selected = false;
            if(option.id == value)
                selected = true;
            select.appendChild(BX.create({
                tag: 'option',
                text: option.label,
                attrs: {value:option.id, selected : selected},
                events:{
                    input : BX.proxy(changeAction, this)
                }
            }))

            viewOptionsCont.appendChild(BX.create({
                tag: 'div',
                text: option.label,
                props: {className : 'select-option'},
                attrs : { value : option.id },
                events: {
                    click : selectAction,
                }
            }))
        })


        questionBody.querySelector('.questionEntry').appendChild(BX.create({
            tag : 'div',
            props: {className: 'life-select'},
            children : [
                input,
                viewOptionsCont,
                select
            ],
        }));
        return questionBody;
    }

    createJSLifeSelectQst(conf, value=''){
        let questionBody = this.createQuestionBody(conf);
        let tool = this;
        let changeAction = function(){
            tool.onInputEvent(event)
        }

        let prevalue = '';
        if(value && conf.list.find(x => x.id == value))
            prevalue = conf.list.find(x => x.id == value).label;

        let inputAction = function(){
            tool.jsLifeSelectOninput(event);
        };

        let selectAction = function(){
            tool.jsLifeSelectOnSelect(event);
        };

        let select = BX.create({
            tag : 'select',
            props : { name : conf.code, 'hidden' : true, className: 'true-select tool-quest-item'},
            events:{
                change : BX.proxy(changeAction, this)
            }
        });


        let input = BX.create({
            tag : 'input',
            props : { className: 'select-head', type : 'text', value : prevalue},
            events : {
                input : BX.proxy(inputAction, this),
                click : BX.proxy(inputAction, this),
            }
        });

        let viewOptionsCont = BX.create({
            tag : 'div',
            props : { className: 'select-options'},
        });

        if(conf.list)
            conf.list.forEach(function(option){
            let selected = false;
            if(option.id == value)
                selected = true;
            select.appendChild(BX.create({
                tag: 'option',
                text: option.label,
                attrs: {value:option.id, selected : selected}
            }))

            viewOptionsCont.appendChild(BX.create({
                tag: 'div',
                text: option.label,
                props: {className : 'select-option'},
                attrs : { value : option.id },
                events: {
                    click : selectAction,
                }
            }))
        })


        questionBody.querySelector('.questionEntry').appendChild(BX.create({
            tag : 'div',
            props: {className: 'life-select'},
            children : [
                input,
                viewOptionsCont,
                select
            ],
        }));
        return questionBody;
    }

    createSelectQst(conf, value=''){
        let questionBody = this.createQuestionBody(conf);
        let tool = this;
        let inputAction = function(){
            tool.onInputEvent(event)
        }

        let prevalue = '';
        if(value && conf.list.find(x => x.id == value))
            prevalue = conf.list.find(x => x.id == value).label;

        let select = BX.create({
            tag : 'select',
            props : { name : conf.code, className : 'tool-quest-item', multiple : conf.multiple, size : conf.size},
            events:{
                change : BX.proxy(inputAction, this)
            }
        });

        if(conf.list)
            conf.list.forEach(function(option){
                let selected = false;
                if(option.id == value)
                    selected = true;

                if(conf.multiple && typeof value == 'object'){
                    if(value.findIndex(x => x== option.id) != -1)
                        selected = true;
                }

                select.appendChild(BX.create({
                    tag: 'option',
                    text: option.label,
                    attrs: {value:option.id, selected : selected}
                }))
            })

        questionBody.querySelector('.questionEntry').appendChild(select)
        return questionBody;
    }

    createIntQst(conf, value=''){
        let questionBody = this.createQuestionBody(conf);
        let tool = this;
        let inputAction = function(){
            tool.onInputEvent(event)
        }

        questionBody.querySelector('.questionEntry').appendChild(BX.create({
            tag : 'input',
            attrs : {type : 'number',name : conf.code, className : 'tool-quest-item', value : value, min : conf.min, max : conf.max},
            events:{
                input : BX.proxy(inputAction, this)
            }
        }))
        return questionBody;
    }

    createTextQst(conf, value=''){
        let questionBody = this.createQuestionBody(conf);
        let tool = this;
        let inputAction = function(){
            tool.onInputEvent(event)
        }

        if(value=='' && conf.default)
            value = conf.default;


        questionBody.querySelector('.questionEntry').appendChild(BX.create({
            tag : 'input',
            props : {type : 'text', name : conf.code, className : 'tool-quest-item', value : value},
            events:{
                input : BX.proxy(inputAction, this)
            }
        }))
        return questionBody;
    }

    createCheckboxQst(conf, value=''){
        let questionBody = this.createQuestionBody(conf);
        let tool = this;
        let inputAction = function(){
            tool.onInputEvent(event)
        }
        let checked = conf.default;

        if(value === 'on' || value === true)
            checked = true;
        else if(value === false)
            checked = false;

        questionBody.querySelector('.questionEntry').appendChild(BX.create({
            tag : 'input',
            props : {type : 'checkbox', name : conf.code, className : 'tool-quest-item', checked : checked},
            events:{
                change : BX.proxy(inputAction, this)
            }
        }))
        return questionBody;
    }

    createQuestionBody(conf){
        return BX.create({
            tag : 'div',
            props : {className : 'tool-quest-item-cont'},
            children : [
                BX.create({
                    tag: 'div',
                    props: {className:'questionlabel'},
                    text : conf.title
                }),
                BX.create({
                    tag: 'div',
                    props: {className:'questionEntry'},
                })
            ]
        });
    }


    /*
     * Events
     */
    LifeSelectOninput(ev, conf){

        let inputed = ev.target.value.toLowerCase();
        let cont = ev.target.closest('.life-select');

        if(inputed.length == 0) {
            cont.querySelector('select').value = '';
            let event = new Event("change");
            cont.querySelector('select').dispatchEvent(event);
        }

        switch (conf.entity.type) {
            case 'user':
                let likeText = ev.target.value;
                this.runCustomFilter({
                    'name' : likeText
                },'getUsers', ev.target, likeText);
        }
    }

    jsLifeSelectOninput(ev){

        let el = ev.target;
        let cont = el.closest('.life-select');
        let optionsCont = cont.querySelector('.select-options');
        let inputed = el.value.toLowerCase();

        if(inputed.length == 0) {
            cont.querySelector('select').value = '';
        }

        let searchPath = '.select-options';

        let name = el.closest('.questionEntry').querySelector('.tool-quest-item').getAttribute('name');
        let conf = this.getConfigItem(name);
        if(conf.selection){
            searchPath = '.select-options[allowed]';
        }

        Array.from(document.querySelectorAll(searchPath)).forEach(function(options){
            options.style.display= 'none';
        })

        optionsCont.style.display="block";
        Array.from(optionsCont.querySelectorAll(searchPath)).forEach(function(option){
            if(option.innerText.toLowerCase().includes(inputed))
                option.style.display = 'flex';
            else
                option.style.display = 'none';
        })
    }

    jsLifeSelectOnSelect(ev){
        let lifeSelectCont = ev.target.closest('.life-select');
        let el = ev.target;

        lifeSelectCont.querySelector('select').value = el.getAttribute('value');
        lifeSelectCont.querySelector('.select-head').value = el.innerText;
        el.closest('.select-options').style.display='none';

        let event = new Event("change");
        lifeSelectCont.querySelector('select').dispatchEvent(event);
    }

    clickListener(event){
        if(!event.target.closest('.select-options') && document.querySelector('.select-options') && !event.target.classList.contains("select-head")){
            Array.from(document.querySelectorAll('.select-options')).forEach(function(options){
                options.style.display= 'none';
            })
        }
    }

    eventPress(e){
        if (e.which == 13 && this.toolBox.document) {
            this.startSaving(this.toolBox.mode == 'create'? '' : this.toolBox.selected);
        }
    }

    onInputEvent(e){

    }

    onBeforeAjax(data){
        return data;
    }

    onAfterAjax(data){

    }

    onBeforeDelete(id){
        return true;
    }

    onAfterDelete(id, data){
        return true;
    }

    onSave(data){
        return data;
    }

    onAfterLoad(elementId, arrayIndex){
        return true;
    }

    onCreateToolBox(box){
        return true;
    }




    /*
     * Items data
     */
    create(data){

        if(this.checkRequireds(data) == false) {
            let tool = this;
            let saveAction = function(){
                tool.startSaving(tool.toolBox.mode == 'create'? '' : tool.toolBox.selected);
            }

            this.toolBox.document.buttons[0].events.click = BX.proxy(saveAction, this);
            this.toolBox.document.buttons[0].button.removeAttribute('disabled');
            this.toolBox.document.buttons[0].disabled = false;
            this.toolBox.document.buttons[0].button.classList.remove('ui-btn-disabled');
            return false;
        }



        let id = this.items.length+1;

        this.createToolItem(id, data);

        data.id = id;

        this.items.push(data);
        return id;
    }

    update(id, data){
        if(this.checkRequireds(data) == false) {
            let tool = this;
            let saveAction = function(){
                tool.startSaving(tool.toolBox.mode == 'create'? '' : tool.toolBox.selected);
            }

            this.toolBox.document.buttons[0].events.click = BX.proxy(saveAction, this);
            this.toolBox.document.buttons[0].button.removeAttribute('disabled');
            this.toolBox.document.buttons[0].disabled = false;
            this.toolBox.document.buttons[0].button.classList.remove('ui-btn-disabled');
            return false;
        }

        this.updateToolItem(id, data);

        let index = this.items.findIndex((obj => obj.id == id));

        for (let code in data){
            this.items[index][code]=data[code];
        }

        return this.items[index];
    }

    getById(id){
        let index = this.items.findIndex((obj => obj.id == id));
        return this.items[index];
    }

    getList(filter={}){
        if(filter) {
            let items = this.items;
            for (let i=0 ; i<this.items.length;i++){
                let item = this.items[i];
                for (let key in filter) {
                    if(item[key] != filter[key])
                        items.splice(i, 1);

                }
            }
            return items;
        }

        return this.items;
    }

    checkRequireds(data){

        let exeption = 0;

        let requireds = this.config.filter(function( obj ) {
            return obj.required;
        });

        for(let index in  requireds){

            let configItem = requireds[index];
            let code = configItem.code;
            let value = data[code];

            if(value.length == 0){
                exeption=1;

                this.uiAlert('Поле "'+configItem.title+'" обязательно для заполнения');
            }

            if(exeption==1)
                return false;
        }
        return true;

    }

    getConfigItem(configCode){

        let index = this.config.findIndex((obj => obj.code == configCode));

        return this.config[index];
    }

    updateConfigItem(configCode, data){

        let index = this.config.findIndex((obj) => obj.code == configCode);
        if( index == undefined || index == null )
            return false;



        for(let code in data){
            if(this.config[index][code])
                this.config[index][code] = data[code];
        }

        return true;
    }

    getToolBoxQuestionEntry(configCode){
        if(!this.toolBox.document)
            return false;

        return this.toolBox.document.message.querySelector('[name='+configCode+']').closest('.questionEntry');
    }

    getToolBoxQuestionInput(configCode){
        if(!this.toolBox.document)
            return false;

        return this.toolBox.document.message.querySelector('[name='+configCode+']');
    }



    /*
     * Ajax
     */

    runCustomFilter(params, method, el, likeText){

        let tool = this;
        el.closest('.questionEntry').appendChild(BX.create({
            tag:'div',
            attrs: {className : 'load'}
        }));
        setTimeout(function(){
            if(likeText == el.value){

                let data = tool.onBeforeAjax({
                    action : method,
                    props : params,
                });

                BX.ajax({
                    url: '/local/lib/js/toolpicker/ajax.php',
                    method: 'POST',
                    data: typeof data === 'object'? data : {
                        action : method,
                        props : params,
                    },
                    onsuccess : function(response) {

                        let selectAction = function(){
                            tool.jsLifeSelectOnSelect(event);
                        };

                        let cont = el.closest('.life-select');
                        let fakeSelect = cont.querySelector('.select-options');
                        let select = cont.querySelector('select');

                        fakeSelect.innerHTML = '';
                        select.innerHTML = '';

                        let answer = JSON.parse(response);

                        let index = tool.config.findIndex((obj => obj.code == select.getAttribute('name')));

                        if(!tool.config[index].list)
                            tool.config[index].list=[];

                        for(let key in answer.list){
                            let optionItem = answer.list[key];

                            fakeSelect.appendChild(BX.create({
                                tag: 'div',
                                attrs : {className : 'select-option', value:optionItem.id},
                                text: optionItem.label,
                                events: {
                                    click : BX.proxy(selectAction, this),
                                }
                            }));

                            select.appendChild(BX.create({
                                tag: 'option',
                                attrs : { value:optionItem.id},
                                text: optionItem.label,
                            }))

                            if(tool.config[index].list.length>0) {
                                if (!tool.config[index].list.find(x => x.id == optionItem.id)) {
                                    tool.config[index].list.push(optionItem)
                                }
                            }else {
                                tool.config[index].list.push(optionItem)
                            }
                        }

                        Array.from(document.querySelectorAll('.select-options')).forEach(function(options){
                            options.style.display= 'none';
                        })

                        fakeSelect.style.display="block";

                        tool.onAfterAjax({
                            query : {
                                url: '/local/lib/js/toolpicker/ajax.php',
                                method: 'POST',
                                data: {
                                    action : method,
                                    props : params,
                                },
                            },
                            anwer : answer,
                        })


                        el.closest('.questionEntry').querySelector('.load').remove();
                        return answer.list;
                    },
                })

            }
            else
                el.closest('.questionEntry').querySelector('.load').remove();
        }, 500)
    }

    /*
     * Inform
     */

    uiAlert(context){
        BX.UI.Notification.Center.notify({
            content: BX.create('div', {
                style: {
                    fontSize : '20px',
                },
                html: context
            }),
            position : 'top-right',
        });
    }
}
