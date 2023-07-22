class entityUserPermissionConfigurator{
    elements;
    deleteIds=[];
    params;
    form;
    renderPlace;
    increment=0;

    constructor(renderPlace, rows, params) {
        this.elements = rows;
        this.params = params;
        this.renderPlace = renderPlace;
        this.renderPlace.innerHTML = '';
        let self = this;

        this.renderPlace.appendChild(BX.create('form', {
            props : {className : 'ui-list minimize entity-users-permission'}
        }));
        this.form = this.renderPlace.querySelector('form');
        this.parse();
        this.createTagSelector();
    }

    parse(){
        let elements = this.elements,
            element;
        this.form.innerHTML = '';

        for(let i in elements){
            element = this.createRow(elements[i]);
        }
    }

    createRow(data){
        let self = this,
            selectOptions = [],
            params = this.params;

        for(let i in params.RULLS_VALUES){
            selectOptions.push(BX.create('option', {
                attrs: {value: i, selected : data.PERMISSION == i},
                text: params.RULLS_VALUES[i]
            }))
        }


        let row = BX.create('div', {
            props : {className : 'row'},
            children: [
                BX.create('div', {
                    props : {className : 'table-data name col-8'},
                    html: data.ENTITY_S,
                }),
                BX.create('div', {
                    props : {className : 'table-data permission col-3'},
                    children: [
                        BX.create('select', {
                            props: {className : 'link-select'},
                            children : selectOptions
                        })
                    ],
                    events: {
                        change : function(event){
                            self.changePermission(event, data.ID);
                        }
                    }
                }),
                BX.create('div', {
                    props : {className : 'col-1 align-items-center d-flex'},
                    children: [
                        BX.create('div', {
                            props : { className : 'remove-link'}
                        })
                    ],
                    events: {
                        click : function(event){
                            self.removeItem(event, data.ID);
                        }
                    }
                }),
            ]
        })

        this.form.appendChild(row);
    }

    createTagSelector(){

        let tabs = [
            {
                id: 'USERS',
                title: 'Пользователи',
                itemOrder: {title:'asc'},
                icon: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9IiMwMDAwMDAiIHdpZHRoPSI4MDBweCIgaGVpZ2h0PSI4MDBweCIgdmlld0JveD0iMCAwIDMyIDMyIiB2ZXJzaW9uPSIxLjEiPg0KICAgIDxwYXRoIGQ9Ik0xNS45OTIgMmMzLjM5NiAwIDYuOTk4IDIuODYgNi45OTggNC45OTV2NC45OTdjMCAxLjkyNC0wLjggNS42MDQtMi45NDUgNy4yOTMtMC41NDcgMC40My0wLjgzMSAxLjExNS0wLjc0OSAxLjgwNyAwLjA4MiAwLjY5MiAwLjUxOCAxLjI5MSAxLjE1MSAxLjU4Mmw4LjcwMyA0LjEyN2MwLjA2OCAwLjAzMSAwLjgzNCAwLjE2IDAuODM0IDEuMjNsMC4wMDEgMS45NTItMjcuOTg0IDAuMDAydi0yLjAyOWMwLTAuNzk1IDAuNTk2LTEuMDQ1IDAuODM1LTEuMTU0bDguNzgyLTQuMTQ1YzAuNjMtMC4yODkgMS4wNjUtMC44ODUgMS4xNDktMS41NzNzLTAuMTkzLTEuMzctMC43MzMtMS44MDNjLTIuMDc4LTEuNjY4LTMuMDQ2LTUuMzM1LTMuMDQ2LTcuMjg3di00Ljk5N2MwLjAwMS0yLjA4OSAzLjYzOC00Ljk5NSA3LjAwNC00Ljk5NXpNMTUuOTkyLTBjLTQuNDE2IDAtOS4wMDQgMy42ODYtOS4wMDQgNi45OTZ2NC45OTdjMCAyLjE4NCAwLjk5NyA2LjYwMSAzLjc5MyA4Ljg0N2wtOC43ODMgNC4xNDVzLTEuOTk4IDAuODktMS45OTggMS45OTl2My4wMDFjMCAxLjEwNSAwLjg5NSAxLjk5OSAxLjk5OCAxLjk5OWgyNy45ODZjMS4xMDUgMCAxLjk5OS0wLjg5NSAxLjk5OS0xLjk5OXYtMy4wMDFjMC0xLjE3NS0xLjk5OS0xLjk5OS0xLjk5OS0xLjk5OWwtOC43MDMtNC4xMjdjMi43Ny0yLjE4IDMuNzA4LTYuNDY0IDMuNzA4LTguODY1di00Ljk5N2MwLTMuMzEtNC41ODItNi45OTUtOC45OTgtNi45OTV2MHoiIHN0eWxlPSImIzEwOyAgICBmaWxsOiAjQUJCMUI4OyYjMTA7Ii8+DQo8L3N2Zz4='
            },
            {
                id: 'GROUPS',
                title: 'Группы пользователей',
                itemOrder: {title:'asc'},
                icon: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9IiNBQkIxQjgiIHdpZHRoPSI4MDBweCIgaGVpZ2h0PSI4MDBweCIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBkPSJNMTkuNzMsMTYuNjYzQTMuNDY3LDMuNDY3LDAsMCwwLDIwLjUsMTQuNWEzLjUsMy41LDAsMCwwLTcsMCwzLjQ2NywzLjQ2NywwLDAsMCwuNzcsMi4xNjNBNi4wNCw2LjA0LDAsMCwwLDEyLDE4LjY5YTYuMDQsNi4wNCwwLDAsMC0yLjI3LTIuMDI3QTMuNDY3LDMuNDY3LDAsMCwwLDEwLjUsMTQuNWEzLjUsMy41LDAsMCwwLTcsMCwzLjQ2NywzLjQ2NywwLDAsMCwuNzcsMi4xNjNBNiw2LDAsMCwwLDEsMjJhMSwxLDAsMCwwLDEsMUgyMmExLDEsMCwwLDAsMS0xQTYsNiwwLDAsMCwxOS43MywxNi42NjNaTTcsMTNhMS41LDEuNSwwLDEsMS0xLjUsMS41QTEuNSwxLjUsMCwwLDEsNywxM1pNMy4xMjYsMjFhNCw0LDAsMCwxLDcuNzQ4LDBaTTE3LDEzYTEuNSwxLjUsMCwxLDEtMS41LDEuNUExLjUsMS41LDAsMCwxLDE3LDEzWm0tMy44NzMsOGE0LDQsMCwwLDEsNy43NDYsMFpNNy4yLDguNEExLDEsMCwwLDAsOC44LDkuNmE0LDQsMCwwLDEsNi40LDAsMSwxLDAsMSwwLDEuNi0xLjIsNiw2LDAsMCwwLTIuMDY1LTEuNzQyQTMuNDY0LDMuNDY0LDAsMCwwLDE1LjUsNC41YTMuNSwzLjUsMCwwLDAtNywwLDMuNDY0LDMuNDY0LDAsMCwwLC43NjUsMi4xNTdBNS45OTQsNS45OTQsMCwwLDAsNy4yLDguNFpNMTIsM2ExLjUsMS41LDAsMSwxLTEuNSwxLjVBMS41LDEuNSwwLDAsMSwxMiwzWiI+PC9wYXRoPjwvc3ZnPg=='
            },
            {
                id: 'OTHER',
                title: 'Другое',
                itemOrder: {title:'asc'},
                icon: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MDBweCIgaGVpZ2h0PSI4MDBweCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjQUJCMUI4Ij4NCjxwYXRoIGQ9Ik03IDVDNyA2LjEwNDU3IDYuMTA0NTcgNyA1IDdDMy44OTU0MyA3IDMgNi4xMDQ1NyAzIDVDMyAzLjg5NTQzIDMuODk1NDMgMyA1IDNDNi4xMDQ1NyAzIDcgMy44OTU0MyA3IDVaIiBmaWxsPSIjQUJCMUI4Ii8+DQo8cGF0aCBkPSJNMTQgNUMxNCA2LjEwNDU3IDEzLjEwNDYgNyAxMiA3QzEwLjg5NTQgNyAxMCA2LjEwNDU3IDEwIDVDMTAgMy44OTU0MyAxMC44OTU0IDMgMTIgM0MxMy4xMDQ2IDMgMTQgMy44OTU0MyAxNCA1WiIgZmlsbD0iI0FCQjFCOCIvPg0KPHBhdGggZD0iTTE5IDdDMjAuMTA0NiA3IDIxIDYuMTA0NTcgMjEgNUMyMSAzLjg5NTQzIDIwLjEwNDYgMyAxOSAzQzE3Ljg5NTQgMyAxNyAzLjg5NTQzIDE3IDVDMTcgNi4xMDQ1NyAxNy44OTU0IDcgMTkgN1oiIGZpbGw9IiNBQkIxQjgiLz4NCjxwYXRoIGQ9Ik03IDEyQzcgMTMuMTA0NiA2LjEwNDU3IDE0IDUgMTRDMy44OTU0MyAxNCAzIDEzLjEwNDYgMyAxMkMzIDEwLjg5NTQgMy44OTU0MyAxMCA1IDEwQzYuMTA0NTcgMTAgNyAxMC44OTU0IDcgMTJaIiBmaWxsPSIjQUJCMUI4Ii8+DQo8cGF0aCBkPSJNMTIgMTRDMTMuMTA0NiAxNCAxNCAxMy4xMDQ2IDE0IDEyQzE0IDEwLjg5NTQgMTMuMTA0NiAxMCAxMiAxMEMxMC44OTU0IDEwIDEwIDEwLjg5NTQgMTAgMTJDMTAgMTMuMTA0NiAxMC44OTU0IDE0IDEyIDE0WiIgZmlsbD0iI0FCQjFCOCIvPg0KPHBhdGggZD0iTTIxIDEyQzIxIDEzLjEwNDYgMjAuMTA0NiAxNCAxOSAxNEMxNy44OTU0IDE0IDE3IDEzLjEwNDYgMTcgMTJDMTcgMTAuODk1NCAxNy44OTU0IDEwIDE5IDEwQzIwLjEwNDYgMTAgMjEgMTAuODk1NCAyMSAxMloiIGZpbGw9IiNBQkIxQjgiLz4NCjxwYXRoIGQ9Ik01IDIxQzYuMTA0NTcgMjEgNyAyMC4xMDQ2IDcgMTlDNyAxNy44OTU0IDYuMTA0NTcgMTcgNSAxN0MzLjg5NTQzIDE3IDMgMTcuODk1NCAzIDE5QzMgMjAuMTA0NiAzLjg5NTQzIDIxIDUgMjFaIiBmaWxsPSIjQUJCMUI4Ii8+DQo8cGF0aCBkPSJNMTQgMTlDMTQgMjAuMTA0NiAxMy4xMDQ2IDIxIDEyIDIxQzEwLjg5NTQgMjEgMTAgMjAuMTA0NiAxMCAxOUMxMCAxNy44OTU0IDEwLjg5NTQgMTcgMTIgMTdDMTMuMTA0NiAxNyAxNCAxNy44OTU0IDE0IDE5WiIgZmlsbD0iI0FCQjFCOCIvPg0KPHBhdGggZD0iTTE5IDIxQzIwLjEwNDYgMjEgMjEgMjAuMTA0NiAyMSAxOUMyMSAxNy44OTU0IDIwLjEwNDYgMTcgMTkgMTdDMTcuODk1NCAxNyAxNyAxNy44OTU0IDE3IDE5QzE3IDIwLjEwNDYgMTcuODk1NCAyMSAxOSAyMVoiIGZpbGw9IiNBQkIxQjgiLz4NCjwvc3ZnPg=='
            }
        ];

        if(this.params.PROJECT_GROUP_EXISTS)
            tabs.push({
                id: 'PROJECT_GROUPS',
                title: 'Группы проектов',
                itemOrder: {title:'asc'},
                icon: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iODAwcHgiIGhlaWdodD0iODAwcHgiIHZpZXdCb3g9IjAgMCAyNCAyNCIgdmVyc2lvbj0iMS4xIj4NCiAgICAgICAgICAgICAgICA8ZyBpZD0iUGFnZS0xIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4NCiAgICAgICAgICAgICAgICAgICAgPGcgaWQ9ImljLWJhZyIgZmlsbC1ydWxlPSJub256ZXJvIiBmaWxsPSIjQUJCMUI4Ij4NCiAgICAgICAgICAgICAgICAgICAgICAgIDxwYXRoIGZpbGw9IiNBQkIxQjgiIGQ9Ik00LDkuMDA4NTMwMiBMNCwxNi45OTE0Njk4IEM0LDE3LjU0ODQwMTMgNC40NDgxNjA0LDE4IDQuOTk1MDg5MjksMTggTDE5LjAwNDkxMDcsMTggQzE5LjU1MzMyNTksMTggMjAsMTcuNTUwMjIxOCAyMCwxNi45OTE0Njk4IEwyMCw5LjAwODUzMDIgQzIwLDguNDUxNTk4NzIgMTkuNTUxODM5Niw4IDE5LjAwNDkxMDcsOCBMNC45OTUwODkyOSw4IEM0LjQ0NjY3NDExLDggNCw4LjQ0OTc3ODIgNCw5LjAwODUzMDIgWiBNMTcsNiBDMTcsNS40NDc3MTUyNSAxNy40NDM4NjQ4LDUgMTgsNSBDMTguNTUyMjg0Nyw1IDE5LDUuNDQzODY0ODIgMTksNiBMMTkuMDA0OTEwNyw2IEMyMC42NTk4NDUzLDYgMjIsNy4zNTA0MzY0NyAyMiw5LjAwODUzMDIgTDIyLDE2Ljk5MTQ2OTggQzIyLDE4LjY1MTcwMjcgMjAuNjYxMDA3OCwyMCAxOS4wMDQ5MTA3LDIwIEw0Ljk5NTA4OTI5LDIwIEMzLjM0MDE1NDczLDIwIDIsMTguNjQ5NTYzNSAyLDE2Ljk5MTQ2OTggTDIsOS4wMDg1MzAyIEMyLDcuMzQ4Mjk3MzQgMy4zMzg5OTIyMiw2IDQuOTk1MDg5MjksNiBMNSw2IEM1LDUuNDQ3NzE1MjUgNS40NDM4NjQ4Miw1IDYsNSBDNi41NTIyODQ3NSw1IDcsNS40NDM4NjQ4MiA3LDYgTDguMTI2MDE3NDksNiBDOC41NzAwNjAyOCw0LjI3NDc3Mjc5IDEwLjEzNjE2MDYsMyAxMiwzIEMxMy44NjM4Mzk0LDMgMTUuNDI5OTM5Nyw0LjI3NDc3Mjc5IDE1Ljg3Mzk4MjUsNiBMMTcsNiBaIE0xMC4yNjc1NjQ0LDYgTDEzLjczMjQzNTYsNiBDMTMuMzg2NjI2Miw1LjQwMjE5ODYzIDEyLjc0MDI4MjQsNSAxMiw1IEMxMS4yNTk3MTc2LDUgMTAuNjEzMzczOCw1LjQwMjE5ODYzIDEwLjI2NzU2NDQsNiBaIiBpZD0iUmVjdGFuZ2xlIj4NCg0KICAgICAgICAgICAgICAgICAgICAgICAgPC9wYXRoPg0KICAgICAgICAgICAgICAgICAgICA8L2c+DQogICAgICAgICAgICAgICAgPC9nPg0KICAgICAgICAgICAgPC9zdmc+'
            });

        let self = this;

        this.renderPlace.appendChild(BX.create({
            tag : 'span',
            props: {className: 'ui-tag-selector-item ui-tag-selector-add-button'},
            html : '<span class="ui-tag-selector-add-button-caption">Добавить</span>'
        }))

        this.addBtn = this.renderPlace.querySelector('.ui-tag-selector-add-button');

        this.Dialog = new BX.UI.EntitySelector.Dialog({
            targetNode: this.addBtn,
            context: self.params.TABLE_NAME+'selector',
            items:  self.params.TAB_ITEMS,
            enableSearch: true,
            tabs:tabs,
            events: {
                'Item:onSelect': (event) => {
                    let data = event.getData().item;
                    
                    self.tagSelector = event.target.tagSelector;
                    self.tagSelector.getContainer().querySelector('.ui-tag-selector-tag-remove').click();

                    self.popUp = self.tagSelector.getContainer().closest('.popup-window');

                    self.onTagSelect(data);
                },

            }
        })

        this.addBtn.addEventListener('click', () => {
            this.Dialog.show();
        })

    }

    removeItem(event, ID){
        if(ID == parseInt(ID)){
            this.deleteIds.push(ID)
        }
        if(event.target)
            event.target.closest('.row').remove();

        let key = this.elements.findIndex(x => x.ID == ID);
        this.elements.shift(key);
    }

    changePermission(event, ID){
        let key = this.elements.findIndex(x => x.ID == ID);

        this.elements[key].PERMISSION = event.target.value;
        this.elements[key].EDITED = true;
    }

    getConfiguration(){
        return this.elements;
    }

    onTagSelect(data){
        let loadData = {
            ID: 'TEMPLATE_' + this.increment,
            ENTITY_S: data.title.text,
            PERMISSION: 'R',
            ENTITY: data.id,
            EDITED: true,
        };

        if(this.elements.find(element => element.ENTITY == data.id)){
            showNotyMessage('Найден дубликат. Попробуйте еще раз.');
            return;
        }

        this.increment++;

        loadData[this.params.REF_COLUMN_NAME] = this.params.COLUMN_VALUE;
        this.createRow(loadData);
        this.elements.push(loadData);

        this.popUp.style.top = this.addBtn.getBoundingClientRect().y+this.addBtn.getBoundingClientRect().height+'px';

  
    }

    save(){
        let params = this.params;
        delete params.TAB_ITEMS;

        if(this.elements.filter(el => el.EDITED).length == 0 && this.deleteIds.length == 0){
            if(BX.SidePanel.Instance.opened)
                BX.SidePanel.Instance.close();
            else
                location.reload();
        }

        BX.ajax.runComponentAction('rs:entity.user.permission.configator', 'loadItems', {
            mode: 'class',
            data: {
                params: params,
                loadItems : this.elements.filter(el => el.EDITED),
                deleteIds : this.deleteIds
            },
        }).then(function (response) {
            if(BX.SidePanel.Instance.opened)
                BX.SidePanel.Instance.close();
            else
                location.reload();
        }, function (response) {
            console.log(response);
        });
    }


}