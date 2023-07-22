class  CoolEditor{
    elements;
    config;

    constructor(config) {
        this.config = config;
        this.buildEditorBodies();
    }

    refreshContent(){
        let self=this;

        if(!this.config.component)
            return;

        for(let i in this.config.componentParams){
            if(i[0] == '~')
                delete(this.config.componentParams[i]);
        }

        BX.ajax.runComponentAction(this.config.component, 'getComponentTemplateResult', {
            mode: 'class',
            data: {
                params: this.config.componentParams,
            },
        }).then(function (response) {
            let parser = new DOMParser(),
                doc = parser.parseFromString(response.data, 'text/html'),
                child = doc.querySelector('body').firstChild;

            document.querySelector('.'+Array.from(child.classList).join('.')).innerHTML = child.innerHTML;
            self.buildEditorBodies();
        }, function (response) {
            showNotyMessage("Произошла ошибка");
        });

    }
    deleteEntity(id, module, type, tableName){
        let self = this;

        let box = new BX.UI.Dialogs.MessageBox({
            message: BX.create({
                tag : 'div',
                attrs : { style : 'font: 17px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
                html : 'Подтвердите удаление',
            }),
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
            okCaption: "ОК",
            onOk : function (messageBox){
                BX.ajax.runComponentAction('rs:entity.edit', 'deleteEntity', {
                    mode: 'class',
                    data: {
                        id: id,
                        module: module,
                        type: type,
                        tableName: tableName,
                    },
                }).then(function (response) {
                    messageBox.close();
                    self.refreshContent();
                }, function (response) {
                    showNotyMessage("Произошла ошибка");
                });
            },
            modal: true,
        })

        box.show();
    }


    buildEditorBodies(){
        this.elements = Array.from(document.querySelectorAll('[cool-edit-here]:not([complited=true])'));

        for(let i in this.elements){
            this.buildEditorBody(this.elements[i].parentNode, this.elements[i]);

            this.elements[i].parentNode.addEventListener('mouseenter', (evt) => {
                this.showAction(evt, evt.target, this.elements[i]);
            })

            this.elements[i].parentNode.addEventListener('mouseleave', (evt) => {
                this.hideAction(evt, evt.target, this.elements[i]);
            })

            this.elements[i].setAttribute('complited', true);
        }
    }

    buildEditorBody(container, editor){
        let btns = Array.from(editor.querySelectorAll('[cool-edit-btn]')),
            icon,
            clickAction,
            afterClickAction,
            boundings = container.getBoundingClientRect();

        editor.style.left = container.clientLeft + 'px';
        editor.style.top = container.clientTop + 'px';

        if(!this.isElementUnderAnother(container, editor)){
            editor.style.left = container.offsetLeft + 'px';
            editor.style.top = container.offsetTop + 'px';
        }


        editor.style.width = boundings.width + 'px';

        for(let i in btns){

            if(btns[i].querySelector('.icon'))
                continue;

            switch (btns[i].dataset.action){
                case 'edit':
                    icon = this.createEditActionIcon();
                    break;

                case 'delete':
                    icon = this.createDeleteActionIcon();
                    break;

                case 'add':
                    icon = this.createAddActionIcon();
                    break;

                case 'user':
                    icon = this.createUserActionIcon();
                    break;

                case 'settings':
                    icon = this.createSettingsActionIcon();
                    break;

                case 'readMore':
                    icon = this.createReadMoreActionIcon();
                    break;

                case 'prevLink':
                    icon = this.createPrevLinkActionIcon();
                    break;

            }

            btns[i].appendChild(icon);
            icon = btns[i].querySelector('.icon');


            switch (btns[i].dataset.type){
                case 'link':
                    icon.addEventListener('click', () => {

                        window.coolEditor = this;
                        if(btns[i].dataset.actionReload)
                            afterClickAction = 'window.coolEditor.refreshContent()';
                        else
                            afterClickAction='';

                        openSidePanel(btns[i].dataset.link, 600,afterClickAction);
                    })
                    break;

                case 'script':
                    icon.addEventListener('click', () => {
                        window.coolEditor = this;
                        eval(btns[i].dataset.script);
                    })
                    break;
            }
        }
    }

    showAction(evt, container, editor){
        this.buildEditorBody(container, editor);
        editor.classList.add('show');
    }

    hideAction(evt, container, editor){
        editor.classList.remove('show');
    }

    createAddActionIcon(){

        switch (this.config.view){

            case 2:
                return BX.create({
                    tag : 'div',
                    props : {classList : 'icon l-btn'},
                    html : '<b> <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="0 0 32 32"> <path d="M9,17h6v6a1,1,0,0,0,2,0V17h6a1,1,0,0,0,0-2H17V9a1,1,0,0,0-2,0v6H9a1,1,0,0,0,0,2Z"/> </svg> </b><span></span>'
                })
                break

            default:
                return BX.create({
                    tag : 'div',
                    props : {classList : 'icon'},
                    html : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26"><g fill="#fff" fill-rule="evenodd" transform="translate(-.5 -.5)"><rect width="11" height="3" x="8" y="12"/><rect width="3" height="11" x="12" y="8"/></g></svg>'
                })
            break;
        }

    }

    createEditActionIcon(){

        switch (this.config.view){

            case 2:
                return BX.create({
                    tag : 'div',
                    props : {classList : 'icon l-btn'},
                    html : '<b> <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="0 0 1920 1920"> <path d="M277.974 49.076c65.267-65.379 171.733-65.49 237.448 0l232.186 232.187 1055.697 1055.809L1919.958 1920l-582.928-116.653-950.128-950.015 79.15-79.15 801.792 801.68 307.977-307.976-907.362-907.474L281.22 747.65 49.034 515.464c-65.379-65.603-65.379-172.069 0-237.448Zm1376.996 1297.96-307.977 307.976 45.117 45.116 384.999 77.023-77.023-385-45.116-45.116ZM675.355 596.258l692.304 692.304-79.149 79.15-692.304-692.305 79.149-79.15ZM396.642 111.88c-14.33 0-28.547 5.374-39.519 16.345l-228.94 228.94c-21.718 21.718-21.718 57.318 0 79.149l153.038 153.037 308.089-308.09-153.037-153.036c-10.972-10.971-25.301-16.345-39.63-16.345Z" fill-rule="evenodd"/> </svg> </b> <span></span>'
                })
                break

            default:
                return BX.create({
                    tag : 'div',
                    props : {classList : 'icon'},
                    html : '<svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none"> <g> <g> <path id="vector" d="M21.0618 5.24219L18.9405 3.12087C17.7689 1.94929 15.8694 1.94929 14.6978 3.12087L3.70656 14.1121C3.22329 14.5954 2.91952 15.2292 2.84552 15.9086L2.45151 19.5264C2.31313 20.7969 3.38571 21.8695 4.65629 21.7311L8.27401 21.3371C8.95345 21.2631 9.58725 20.9594 10.0705 20.4761L21.0618 9.48483C22.2334 8.31326 22.2334 6.41376 21.0618 5.24219Z" fill="#FFF"></path> <path id="vector_2" d="M21.0618 5.24219L18.9405 3.12087C17.7689 1.94929 15.8694 1.94929 14.6978 3.12087L12.3644 5.45432L18.7283 11.8183L21.0618 9.48483C22.2334 8.31326 22.2334 6.41376 21.0618 5.24219Z" fill="#000000"></path> </g> </g> </svg>'
                })
                break;
        }

    }

    createDeleteActionIcon(){

        switch (this.config.view){

            case 2:
                return BX.create({
                    tag : 'div',
                    props : {classList : 'icon l-btn'},
                    html : '<b> <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#000000" version="1.1" id="Layer_1" width="800px" height="800px" viewBox="0 0 70 70" enable-background="new 0 0 70 70" xml:space="preserve"> <g> <g> <path d="M18.041,14.021c1.013,0,2.021,0.385,2.79,1.153l14.196,14.142l14.142-14.142c0.77-0.769,1.778-1.152,2.791-1.152    c1.024,0,2.053,0.394,2.839,1.18c1.563,1.562,1.574,4.082,0.027,5.63L40.685,34.973l14.142,14.196    c1.547,1.547,1.535,4.068-0.026,5.631c-0.785,0.785-1.813,1.178-2.839,1.178c-1.013,0-2.022-0.383-2.792-1.152L35.027,40.63    L20.831,54.825c-0.769,0.77-1.778,1.154-2.791,1.154c-1.024,0-2.054-0.395-2.839-1.18c-1.563-1.563-1.574-4.084-0.027-5.631    l14.197-14.196L15.174,20.831c-1.547-1.547-1.533-4.068,0.027-5.63C15.987,14.415,17.016,14.021,18.041,14.021 M18.041,10.021    L18.041,10.021c-2.138,0-4.151,0.835-5.667,2.351c-3.12,3.121-3.132,8.185-0.028,11.287l11.363,11.319L12.346,46.339    c-3.105,3.107-3.092,8.172,0.028,11.289c1.514,1.516,3.526,2.352,5.666,2.352c2.126,0,4.121-0.826,5.62-2.326l11.362-11.361    l11.313,11.355c1.505,1.504,3.5,2.33,5.626,2.33c2.138,0,4.15-0.834,5.666-2.35c3.12-3.121,3.132-8.184,0.027-11.287    L46.336,34.978L57.654,23.66c3.104-3.106,3.092-8.17-0.028-11.287c-1.514-1.516-3.526-2.351-5.666-2.351    c-2.124,0-4.119,0.825-5.618,2.323l-11.32,11.319L23.654,12.34C22.162,10.847,20.166,10.022,18.041,10.021L18.041,10.021z"/> </g> <g> <path d="M50.7,21.714c-0.256,0-0.512-0.098-0.707-0.293c-0.391-0.391-0.391-1.023,0-1.414l2.121-2.121    c0.391-0.391,1.023-0.391,1.414,0s0.391,1.023,0,1.414l-2.121,2.121C51.212,21.617,50.956,21.714,50.7,21.714z"/> </g> <g> <path d="M40.801,31.614c-0.256,0-0.512-0.098-0.707-0.293c-0.391-0.391-0.391-1.023,0-1.414l7.07-7.07    c0.391-0.391,1.023-0.391,1.414,0s0.391,1.023,0,1.414l-7.07,7.07C41.313,31.516,41.057,31.614,40.801,31.614z"/> </g> </g> </svg> </b> <span></span>'
                })
                break

            default:
                return BX.create({
                    tag : 'div',
                    props : {classList : 'icon'},
                    html : '<svg xmlns="http://www.w3.org/2000/svg" fill="#FFF" width="800px" height="800px" viewBox="0 0 24 24"> <path d="M17,4V5H15V4H9V5H7V4A2,2,0,0,1,9,2h6A2,2,0,0,1,17,4Z"></path><path d="M20,6H4A1,1,0,0,0,4,8H5V20a2,2,0,0,0,2,2H17a2,2,0,0,0,2-2V8h1a1,1,0,0,0,0-2ZM11,17a1,1,0,0,1-2,0V11a1,1,0,0,1,2,0Zm4,0a1,1,0,0,1-2,0V11a1,1,0,0,1,2,0Z"></path></svg>'
                })
        }

    }

    createSettingsActionIcon(){

        switch (this.config.view){

            case 2:
                return BX.create({
                    tag : 'div',
                    props : {classList : 'icon l-btn'},
                    html : '<b> <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none"> <path d="M14 5.28988H13C13 5.7323 13.2907 6.12213 13.7148 6.24833L14 5.28988ZM15.3302 5.84137L14.8538 6.72058C15.2429 6.93144 15.7243 6.86143 16.0373 6.54847L15.3302 5.84137ZM16.2426 4.92891L15.5355 4.2218V4.2218L16.2426 4.92891ZM17.6569 4.92891L16.9498 5.63601L16.9498 5.63602L17.6569 4.92891ZM19.0711 6.34312L19.7782 5.63602V5.63602L19.0711 6.34312ZM19.0711 7.75734L18.364 7.05023L19.0711 7.75734ZM18.1586 8.66978L17.4515 7.96268C17.1386 8.27563 17.0686 8.75709 17.2794 9.14621L18.1586 8.66978ZM18.7101 10L17.7517 10.2853C17.8779 10.7093 18.2677 11 18.7101 11V10ZM18.7101 14V13C18.2677 13 17.8779 13.2907 17.7517 13.7148L18.7101 14ZM18.1586 15.3302L17.2794 14.8538C17.0686 15.2429 17.1386 15.7244 17.4515 16.0373L18.1586 15.3302ZM19.0711 16.2427L19.7782 15.5356V15.5356L19.0711 16.2427ZM19.0711 17.6569L18.364 16.9498L18.364 16.9498L19.0711 17.6569ZM17.6569 19.0711L18.364 19.7782V19.7782L17.6569 19.0711ZM15.3302 18.1586L16.0373 17.4515C15.7243 17.1386 15.2429 17.0686 14.8538 17.2794L15.3302 18.1586ZM14 18.7101L13.7148 17.7517C13.2907 17.8779 13 18.2677 13 18.7101H14ZM10 18.7101H11C11 18.2677 10.7093 17.8779 10.2853 17.7517L10 18.7101ZM8.6698 18.1586L9.14623 17.2794C8.7571 17.0685 8.27565 17.1385 7.96269 17.4515L8.6698 18.1586ZM7.75736 19.071L7.05026 18.3639L7.05026 18.3639L7.75736 19.071ZM6.34315 19.071L5.63604 19.7782H5.63604L6.34315 19.071ZM4.92894 17.6568L4.22183 18.3639H4.22183L4.92894 17.6568ZM4.92894 16.2426L4.22183 15.5355H4.22183L4.92894 16.2426ZM5.84138 15.3302L6.54849 16.0373C6.86144 15.7243 6.93146 15.2429 6.7206 14.8537L5.84138 15.3302ZM5.28989 14L6.24835 13.7147C6.12215 13.2907 5.73231 13 5.28989 13V14ZM5.28989 10V11C5.73231 11 6.12215 10.7093 6.24835 10.2852L5.28989 10ZM5.84138 8.66982L6.7206 9.14625C6.93146 8.75712 6.86145 8.27567 6.54849 7.96272L5.84138 8.66982ZM4.92894 7.75738L4.22183 8.46449H4.22183L4.92894 7.75738ZM4.92894 6.34317L5.63605 7.05027H5.63605L4.92894 6.34317ZM6.34315 4.92895L7.05026 5.63606L7.05026 5.63606L6.34315 4.92895ZM7.75737 4.92895L8.46447 4.22185V4.22185L7.75737 4.92895ZM8.6698 5.84139L7.9627 6.54849C8.27565 6.86145 8.7571 6.93146 9.14623 6.7206L8.6698 5.84139ZM10 5.28988L10.2853 6.24833C10.7093 6.12213 11 5.7323 11 5.28988H10ZM11 2C9.89545 2 9.00002 2.89543 9.00002 4H11V4V2ZM13 2H11V4H13V2ZM15 4C15 2.89543 14.1046 2 13 2V4H15ZM15 5.28988V4H13V5.28988H15ZM15.8066 4.96215C15.3271 4.70233 14.8179 4.48994 14.2853 4.33143L13.7148 6.24833C14.1132 6.36691 14.4944 6.52587 14.8538 6.72058L15.8066 4.96215ZM15.5355 4.2218L14.6231 5.13426L16.0373 6.54847L16.9498 5.63602L15.5355 4.2218ZM18.364 4.2218C17.5829 3.44075 16.3166 3.44075 15.5355 4.2218L16.9498 5.63602V5.63601L18.364 4.2218ZM19.7782 5.63602L18.364 4.2218L16.9498 5.63602L18.364 7.05023L19.7782 5.63602ZM19.7782 8.46444C20.5592 7.68339 20.5592 6.41706 19.7782 5.63602L18.364 7.05023L18.364 7.05023L19.7782 8.46444ZM18.8657 9.37689L19.7782 8.46444L18.364 7.05023L17.4515 7.96268L18.8657 9.37689ZM19.6686 9.71475C19.5101 9.18211 19.2977 8.67285 19.0378 8.19335L17.2794 9.14621C17.4741 9.50555 17.6331 9.8868 17.7517 10.2853L19.6686 9.71475ZM18.7101 11H20V9H18.7101V11ZM20 11H22C22 9.89543 21.1046 9 20 9V11ZM20 11V13H22V11H20ZM20 13V15C21.1046 15 22 14.1046 22 13H20ZM20 13H18.7101V15H20V13ZM19.0378 15.8066C19.2977 15.3271 19.5101 14.8179 19.6686 14.2852L17.7517 13.7148C17.6331 14.1132 17.4741 14.4944 17.2794 14.8538L19.0378 15.8066ZM19.7782 15.5356L18.8657 14.6231L17.4515 16.0373L18.364 16.9498L19.7782 15.5356ZM19.7782 18.364C20.5592 17.5829 20.5592 16.3166 19.7782 15.5356L18.364 16.9498H18.364L19.7782 18.364ZM18.364 19.7782L19.7782 18.364L18.364 16.9498L16.9498 18.364L18.364 19.7782ZM15.5355 19.7782C16.3166 20.5592 17.5829 20.5592 18.364 19.7782L16.9498 18.364L15.5355 19.7782ZM14.6231 18.8657L15.5355 19.7782L16.9498 18.364L16.0373 17.4515L14.6231 18.8657ZM14.2853 19.6686C14.8179 19.5101 15.3271 19.2977 15.8066 19.0378L14.8538 17.2794C14.4944 17.4741 14.1132 17.6331 13.7148 17.7517L14.2853 19.6686ZM15 20V18.7101H13V20H15ZM13 22C14.1046 22 15 21.1046 15 20H13V22ZM11 22H13V20H11V22ZM9.00002 20C9.00002 21.1046 9.89545 22 11 22V20H9.00002ZM9.00002 18.7101V20H11V18.7101H9.00002ZM8.19337 19.0378C8.67287 19.2977 9.18213 19.5101 9.71477 19.6686L10.2853 17.7517C9.88681 17.6331 9.50557 17.4741 9.14623 17.2794L8.19337 19.0378ZM8.46447 19.7782L9.3769 18.8657L7.96269 17.4515L7.05026 18.3639L8.46447 19.7782ZM5.63604 19.7782C6.41709 20.5592 7.68342 20.5592 8.46447 19.7781L7.05026 18.3639L5.63604 19.7782ZM4.22183 18.3639L5.63604 19.7782L7.05026 18.3639L5.63604 16.9497L4.22183 18.3639ZM4.22183 15.5355C3.44078 16.3166 3.44078 17.5829 4.22183 18.3639L5.63604 16.9497V16.9497L4.22183 15.5355ZM5.13427 14.6231L4.22183 15.5355L5.63604 16.9497L6.54849 16.0373L5.13427 14.6231ZM4.33144 14.2852C4.48996 14.8179 4.70234 15.3271 4.96217 15.8066L6.7206 14.8537C6.52589 14.4944 6.36693 14.1132 6.24835 13.7147L4.33144 14.2852ZM5.28989 13H4V15H5.28989V13ZM4 13H4H2C2 14.1046 2.89543 15 4 15V13ZM4 13V11H2V13H4ZM4 11V9C2.89543 9 2 9.89543 2 11H4ZM4 11H5.28989V9H4V11ZM4.96217 8.1934C4.70235 8.67288 4.48996 9.18213 4.33144 9.71475L6.24835 10.2852C6.36693 9.88681 6.52589 9.50558 6.7206 9.14625L4.96217 8.1934ZM4.22183 8.46449L5.13428 9.37693L6.54849 7.96272L5.63605 7.05027L4.22183 8.46449ZM4.22183 5.63606C3.44078 6.41711 3.44079 7.68344 4.22183 8.46449L5.63605 7.05027L5.63605 7.05027L4.22183 5.63606ZM5.63605 4.22185L4.22183 5.63606L5.63605 7.05027L7.05026 5.63606L5.63605 4.22185ZM8.46447 4.22185C7.68343 3.4408 6.4171 3.4408 5.63605 4.22185L7.05026 5.63606V5.63606L8.46447 4.22185ZM9.37691 5.13428L8.46447 4.22185L7.05026 5.63606L7.9627 6.54849L9.37691 5.13428ZM9.71477 4.33143C9.18213 4.48995 8.67287 4.70234 8.19337 4.96218L9.14623 6.7206C9.50557 6.52588 9.88681 6.36692 10.2853 6.24833L9.71477 4.33143ZM9.00002 4V5.28988H11V4H9.00002Z" fill="#000000"/> <circle cx="12" cy="12" r="3" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> </svg> </b> <span></span>'
                })
                break

            default:
                return BX.create({
                    tag : 'div',
                    props : {classList : 'icon'},
                    html : '<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26"><path fill="#fff" d="M15.9549333,13.6581413 C15.7131867,14.7945707 14.8195067,15.6882747 13.68312,15.9299507 C11.5165333,16.39074 9.6344267,14.508024 10.0952933,12.3418347 C10.31324,11.317524 11.34252,10.2881373 12.3668,10.0701413 C14.5329733,9.6090733 16.4158133,11.4913707 15.9549333,13.6581413 M20.4145333,11.8349653 L18.8296133,11.57054 C18.7170133,11.1101933 18.554,10.6696867 18.3424133,10.2579133 C18.33352,10.2405893 18.3355867,10.219748 18.3478133,10.2045907 L19.3423333,8.9714347 C19.5581333,8.7055427 19.5483333,8.322992 19.3208267,8.0663907 L18.6904667,7.35694 C18.4619733,7.1005947 18.0830667,7.0463867 17.79404,7.228988 L16.4386133,8.08106 C15.8510133,7.67052 15.18652,7.365788 14.4700133,7.1862373 C14.4510133,7.181464 14.4366,7.165956 14.43336,7.1466053 L14.1735067,5.584968 C14.1178533,5.2475667 13.82588,5 13.4831467,5 L12.5332133,5 C12.1911733,5 11.8982,5.2475667 11.8437867,5.584968 L11.5824933,7.147188 C11.5792533,7.1664907 11.5648667,7.181952 11.5458933,7.1867027 C10.96604,7.3320253 10.4214,7.5613827 9.92248,7.861248 C9.9056667,7.8713547 9.8845467,7.8706093 9.8685733,7.8592 L8.6058133,6.957996 C8.3280133,6.7595147 7.9464267,6.7905533 7.7039867,7.0324853 L7.0328933,7.7040747 C6.79096,7.9465187 6.75992,8.3281147 6.9589333,8.6059293 L7.8622533,9.8712173 C7.87364,9.887168 7.8744267,9.9082867 7.8643467,9.9250987 C7.5674,10.420164 7.3404267,10.9615653 7.1952533,11.536148 C7.1904533,11.5551253 7.175,11.5694693 7.15572,11.5726827 L5.5849333,11.8349893 C5.2480533,11.8906173 5,12.1826347 5,12.525392 L5,13.474608 C5,13.8173413 5.2480533,14.1093827 5.5849333,14.1650107 L7.1556667,14.4272947 C7.1749733,14.4305307 7.1904533,14.4448987 7.1952267,14.4638987 C7.31332,14.933 7.4815333,15.3817267 7.7026667,15.7994387 C7.71188,15.816856 7.7099333,15.8380213 7.6975467,15.8533667 L6.7079333,17.0790467 C6.49288,17.344684 6.5019067,17.7274907 6.7294267,17.9838587 L7.3592933,18.693332 C7.5878,18.950144 7.96692,19.0033733 8.2559733,18.8212373 L9.6037467,17.974568 C9.6202667,17.9641827 9.64132,17.9646027 9.65748,17.9755467 C10.23272,18.3642907 10.88164,18.6505573 11.5771867,18.8212373 L11.8438133,20.4149853 C11.8982267,20.7524093 12.1911733,21 12.5332133,21 L13.4831467,21 C13.8258933,21 14.1178533,20.7524333 14.1735067,20.4150093 L14.4338,18.853068 C14.43704,18.8337413 14.4514533,18.8182573 14.4704533,18.8135067 C15.0428133,18.6701867 15.5803733,18.44488 16.0745867,18.1503013 C16.0914,18.140288 16.11244,18.14108 16.1283467,18.1524427 L17.44492,19.0927653 C17.72224,19.2919693 18.1040933,19.26072 18.34628,19.01802 L19.0176667,18.3466413 C19.2593067,18.1049413 19.2920267,17.7235547 19.09136,17.445228 L18.15412,16.1311347 C18.1427333,16.115184 18.14196,16.094088 18.15204,16.0772987 C18.4498667,15.5805587 18.6780533,15.0381093 18.8224267,14.4609413 C18.8271733,14.4419413 18.8426533,14.427528 18.86196,14.4242907 L20.4150667,14.1649653 C20.7529467,14.1093133 21,13.8172947 21,13.4745613 L21,12.5253453 C20.9994933,12.1826347 20.75244,11.8906173 20.4145333,11.8349653"/></svg>'
                })
        }

    }

    createReadMoreActionIcon(){
        return BX.create({
            tag : 'div',
            props : {classList : 'icon l-btn'},
            html : '<b>  <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none"> <path d="M10 16L14 12L10 8" stroke="#200E32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> </svg> </b> <span></span>'
        })
    }

    createPrevLinkActionIcon(){
        return BX.create({
            tag : 'div',
            props : {classList : 'icon l-btn', style:'pading-bottom:3px;'},
            html : '<b> <svg xmlns="http://www.w3.org/2000/svg" width="800px" height="800px" viewBox="0 0 24 24" fill="none"> <path d="M14 8L10 12L14 16" stroke="#200E32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/> </svg> </b> <span></span>'
        })
    }

    createUserActionIcon(){
        return BX.create({
            tag : 'div',
            props : {classList : 'icon l-btn'},
            html : '<b>  <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="0 0 32 32" version="1.1"> <title>user</title> <path d="M4 28q0 0.832 0.576 1.44t1.44 0.576h20q0.8 0 1.408-0.576t0.576-1.44q0-1.44-0.672-2.912t-1.76-2.624-2.496-2.144-2.88-1.504q1.76-1.088 2.784-2.912t1.024-3.904v-1.984q0-3.328-2.336-5.664t-5.664-2.336-5.664 2.336-2.336 5.664v1.984q0 2.112 1.024 3.904t2.784 2.912q-1.504 0.544-2.88 1.504t-2.496 2.144-1.76 2.624-0.672 2.912z"/> </svg> </b> <span></span>'
        })
    }

    isElementUnderAnother(el1, el2) {

        let el1B = el1.getBoundingClientRect(),
            el2B = el2.getBoundingClientRect(),
            element1Top = el1B.x,
            element1Bottom = el1B.x + el1B.height,
            element1Left = el1B.y,
            element1Right = el1B.y + el1B.width,
            element2Top = el2B.x,
            element2Bottom = el2B.x + el2B.height,
            element2Left = el2B.y,
            element2Right = el2B.y + el2B.width;

        return (element1Top <= element2Top && element1Bottom >= element2Top) || (element2Top <= element1Top && element2Bottom >= element1Top);
    };

    getFirstLvlChildren(obj){
        var objChild = [] ;
        var objs = obj.getElementsByTagName('*');
        for(var i=0,j=objs.length; i<j;++i){
            if(objs[i].nodeType != 1){alert(objs[i].nodeType);
                continue ;
            }
            var temp = objs[i].parentNode;
            if(temp.nodeType == 1){
                if(temp == obj){
                    objChild[objChild.length] = objs[i] ;
                }
            }else if(temp.parentNode == obj){
                objChild[objChild.length] = objs[i] ;
            }
        }
        return objChild ;
    }
}
