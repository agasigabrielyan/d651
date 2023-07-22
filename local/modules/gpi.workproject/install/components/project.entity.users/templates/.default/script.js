class someDev7Js123
{
    id;
    workarea;
    grid;

    constructor(data){
        this.id = data.containerId;
    }

    getWorkarea(){
        if(!this.workarea)
            this.workarea =  document.querySelector('.main-grid-table');

        return this.workarea;
    }

    getGrid(){
        if(!this.grid)
            this.grid =  BX.Main.gridManager.getInstanceById(this.id);

        return this.grid;
    }

    getGridRows(){
        return Array.from(this.getWorkarea().querySelectorAll('tr'));
    }

    getGridTemplateRows(){
        let elements = Array.from(this.getWorkarea().querySelectorAll('.main-grid-row-checked.main-grid-not-count'));
        let result = [];
        for(let i in elements){

            let data = {
                node : elements[i],
                id   : elements[i].getAttribute('data-id'),
            };
            result.push(data);
        }
        return result;
    }

    getGridTemplateRowsValues(){
        let rows = this.getGridTemplateRows();

        let data = [];

        for (let i in rows){
            let row = rows[i];
            let cells = Array.from(row.node.querySelectorAll('.main-grid-editor'));

            let rowData = {};
            for (let j in cells){
                let cell = cells[j];
                let cellParam = cell.getAttribute('name');

                if(cell.tagName == 'INPUT' && cell.getAttribute('type') == 'checkbox'){
                    rowData[cellParam] = cell.checked;
                }else if(cell.tagName == 'INPUT'){
                    rowData[cellParam] = cell.value;
                }else{
                    rowData[cellParam] = cell.getAttribute('data-value');
                }

            }
            data.push(rowData);
        }
        return data;
    }

    getGridSelectedIds(){
        return window.PlayersJSClass.getGrid().getActionsPanel().getSelectedIds();
    }

    getGridSellectedRows(){
        let elements = Array.from(this.getWorkarea().querySelectorAll('.main-grid-row-checked.main-grid-row-edit'));
        let result = [];
        for(let i in elements){

            let data = {
                node : elements[i],
                id   : elements[i].getAttribute('data-id'),
            };
            result.push(data);
        }
        return result;
    }

    getGridSellectedValues(){
        let rows = this.getGridSellectedRows();

        let data = [];

        for (let i in rows){
            let row = rows[i];
            let cells = Array.from(row.node.querySelectorAll('.main-grid-editor'));

            let rowData = { 'ID' : row.node.getAttribute('data-id')};
            for (let j in cells){
                let cell = cells[j];
                let cellParam = cell.getAttribute('name');

                if(cell.tagName == 'INPUT' && cell.getAttribute('type') == 'checkbox'){
                    rowData[cellParam] = cell.checked;
                }else if(cell.tagName == 'INPUT'){
                    rowData[cellParam] = cell.value;
                }else{
                    rowData[cellParam] = cell.getAttribute('data-value');
                }

            }
            data.push(rowData);
        }
        return data;
    }

    getGridColums(){
        return this.getGrid().arParams.COLUMNS_ALL;
    }

    getGridEditedRowsValue(){
        return window.PlayersJSClass.getGrid().getRowEditorValue();
    }



    createGridRow(){
        this.getGrid().appendRowEditor();

        let addRowsData = this.getGridTemplateRows();
        this.setUserRender(addRowsData);
    }

    saveGridRows(){
        let self = this;
        let saveRowsData = this.getGridSellectedValues();

        let addRowsData = this.getGridTemplateRowsValues();

        for(let i in saveRowsData){
            console.log(saveRowsData[i]);
            if(saveRowsData[i].USER_ID == ''){
                showNotyMessage('Необходимо выбрать работника', "window.PlayersJSClass.getGrid().container.querySelector('.main-grid-action-panel').classList.remove('main-grid-disable')");
                window.PlayersJSClass.getGrid().container.querySelector('.main-grid-action-panel').classList.remove('main-grid-disable');
                return false;
            }

            if(saveRowsData[i].GROUPS == '[]'){
                showNotyMessage('Для добавления участника необходимо выбрать Группу.', "window.PlayersJSClass.getGrid().container.querySelector('.main-grid-action-panel').classList.remove('main-grid-disable')");
                window.PlayersJSClass.getGrid().container.querySelector('.main-grid-action-panel').classList.remove('main-grid-disable');
                return false;
            }

        }

        for(let i in addRowsData){

            if(addRowsData[i].USER_ID == ''){
                showNotyMessage('Необходимо выбрать работника', "window.PlayersJSClass.getGrid().container.querySelector('.main-grid-action-panel').classList.remove('main-grid-disable')");
                window.PlayersJSClass.getGrid().container.querySelector('.main-grid-action-panel').classList.remove('main-grid-disable');
                return false;
            }

            if(addRowsData[i].GROUPS == '[]'){
                showNotyMessage('Для добавления участника необходимо выбрать Группу.', "window.PlayersJSClass.getGrid().container.querySelector('.main-grid-action-panel').classList.remove('main-grid-disable')");
                window.PlayersJSClass.getGrid().container.querySelector('.main-grid-action-panel').classList.remove('main-grid-disable');
                return false;
            }

        }

        BX.ajax.runComponentAction('rs:project.entity.users', 'sendUsersList', {
            mode: 'class',
            data: {
                add : addRowsData,
                update : saveRowsData,
                projectId : window.project_id,
            }
        }).then(function (response) {
            self.refreshGrid();
        });
        //location.reload();
    }

    removeGridRowTemplates(){
        let templates = this.getGridTemplateRows();
        for(let i in templates){
            templates[i].node.remove();
        }
    }

    deleteGridRows(){

        let deleteRowsData = this.getGridSellectedValues();

        let rows = this.getGridSellectedRows();

        for( let i in rows){
            rows[i].node.remove();
        }

        BX.ajax.runComponentAction('rs:project.entity.users', 'deleteUsers', {
            mode: 'class',
            data: {
                delete : deleteRowsData,
            }
        }).then(function (response) {
            self.refreshGrid();
        });

    }

    refreshGrid(){
        let workarea = this.getWorkarea();
        window.PlayersJSClass.getGrid().getLoader().show();
        let self = this;

        BX.ajax({
            url: window.grid_location,
            method: 'POST',
            data : {
                GRID_ID: self.id,
            },
            processData: true,
            onsuccess: function(data){
                window.PlayersJSClass.getGrid().getLoader().hide();

                let parser = new DOMParser();
                let doc = parser.parseFromString(data, 'text/html');
                document.querySelector('.main-grid').innerHTML = doc.querySelector('.main-grid').innerHTML;

            },
            onfailure: function(e){
                console.log(e);
            }
        });
    }



    onGridEditApply(){
        this.setAllRowsCustomFieldsEditCont();
        this.ensureConstUserCategories();
    }
    onGridAddApply(){
        this.ensureConstUserCategories();
    }


    setAllRowsCustomFieldsEditCont(){

        let columns = this.getGridColums();
        let customColumns = Object.filter(columns, x => x.type == 'CUSTOM');

        if(!customColumns)
            return true;

        let rows = this.getGridSellectedRows();

        this.setUserRender(rows);
        return;
        for (let i in rows){
            let row = rows[i];
            this.setRowNodeCustomFieldsEditCont(row.node, customColumns);
        }
    }

    setRowNodeCustomFieldsEditCont(){
    }

    setUserRender(rows){

        let self = this;

        for(let i in rows){

            if(rows[i].node == null)
                continue;

            let input = rows[i].node.querySelector('input[name=USER_ID]');

            let renderPlace = rows[i].node.querySelector('input[name=USER_ID]').parentNode;

            if(renderPlace == null || renderPlace.querySelector('.ui-tag-selector-item'))
                continue;

            renderPlace.querySelector('.main-grid-editor').hidden = true;

            let arFields = {
                id: 'USER_ID',
                multiple: false,
                dialogOptions: {
                    context: 'MY_MODULE_CONTEXT',
                    items: managersList,
                    tabs:[ {
                        id: 'US_LIST',
                        title: 'Пользователи',
                        itemOrder: {title:'asc'},
                        icon: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGZpbGw9IiMwMDAwMDAiIHdpZHRoPSI4MDBweCIgaGVpZ2h0PSI4MDBweCIgdmlld0JveD0iMCAwIDMyIDMyIiB2ZXJzaW9uPSIxLjEiPg0KICAgIDxwYXRoIGQ9Ik0xNS45OTIgMmMzLjM5NiAwIDYuOTk4IDIuODYgNi45OTggNC45OTV2NC45OTdjMCAxLjkyNC0wLjggNS42MDQtMi45NDUgNy4yOTMtMC41NDcgMC40My0wLjgzMSAxLjExNS0wLjc0OSAxLjgwNyAwLjA4MiAwLjY5MiAwLjUxOCAxLjI5MSAxLjE1MSAxLjU4Mmw4LjcwMyA0LjEyN2MwLjA2OCAwLjAzMSAwLjgzNCAwLjE2IDAuODM0IDEuMjNsMC4wMDEgMS45NTItMjcuOTg0IDAuMDAydi0yLjAyOWMwLTAuNzk1IDAuNTk2LTEuMDQ1IDAuODM1LTEuMTU0bDguNzgyLTQuMTQ1YzAuNjMtMC4yODkgMS4wNjUtMC44ODUgMS4xNDktMS41NzNzLTAuMTkzLTEuMzctMC43MzMtMS44MDNjLTIuMDc4LTEuNjY4LTMuMDQ2LTUuMzM1LTMuMDQ2LTcuMjg3di00Ljk5N2MwLjAwMS0yLjA4OSAzLjYzOC00Ljk5NSA3LjAwNC00Ljk5NXpNMTUuOTkyLTBjLTQuNDE2IDAtOS4wMDQgMy42ODYtOS4wMDQgNi45OTZ2NC45OTdjMCAyLjE4NCAwLjk5NyA2LjYwMSAzLjc5MyA4Ljg0N2wtOC43ODMgNC4xNDVzLTEuOTk4IDAuODktMS45OTggMS45OTl2My4wMDFjMCAxLjEwNSAwLjg5NSAxLjk5OSAxLjk5OCAxLjk5OWgyNy45ODZjMS4xMDUgMCAxLjk5OS0wLjg5NSAxLjk5OS0xLjk5OXYtMy4wMDFjMC0xLjE3NS0xLjk5OS0xLjk5OS0xLjk5OS0xLjk5OWwtOC43MDMtNC4xMjdjMi43Ny0yLjE4IDMuNzA4LTYuNDY0IDMuNzA4LTguODY1di00Ljk5N2MwLTMuMzEtNC41ODItNi45OTUtOC45OTgtNi45OTV2MHoiIHN0eWxlPSImIzEwOyAgICBmaWxsOiAjQUJCMUI4OyYjMTA7Ii8+DQo8L3N2Zz4='
                    }]
                },
                events: {
                    onAfterTagAdd: function (event) {
                        let userId = event.getData().tag.id;
                        let inputHidden = event.getTarget().getContainer().closest('.main-grid-editor-container').querySelector('.main-grid-editor');
                        inputHidden.value = userId;

                        let saveRowsData = self.getGridSellectedValues();

                        let addRowsData = self.getGridTemplateRowsValues();

                    },
                    onTagRemove: function (event) {
                        let inputHidden = event.getTarget().getContainer().closest('.main-grid-editor-container').querySelector('.main-grid-editor');
                        inputHidden.value = '';

                    }
                }
            };


            if(input.value){
                arFields.items = [
                    {
                        id : input.value,
                        title : managersList[managersList.findIndex(x => parseInt(x.id) == parseInt(input.value))].title,
                        entityId : 'userB',
                    }
                ];
                arFields.readonly = true;
            }

            let tagSelector = new BX.UI.EntitySelector.TagSelector(arFields);
            tagSelector.renderTo(renderPlace);
            window.wwS = tagSelector;


              /*BX.ajax.runComponentAction('rs:project.entity.users', 'getEmployyerInput', {
                mode: 'class',
                data: {
                    value : input.value,
                    multiple: 'N',
                    code : 'USER_ID'
                }
            }).then(function (response) {
                console.log(response);
                renderPlace.innerHTML = response['data'];

                //Enable asleep scripts
                let responseHtml = new DOMParser().parseFromString(response['data'], "text/html");
                let innerScripts =  Array.from(responseHtml.querySelectorAll('script'));
                for(let j in innerScripts){
                    var script= document.createElement('script');
                    script.innerHTML = innerScripts[j].innerHTML;
                    document.querySelector('head').appendChild(script);
                }
            });*/
        }

    }

    ensureConstUserCategories(){
        let rows = this.getGridSellectedRows(),
            tryFindItemHolder,
            tryFindItemCheckboxesPopupIndex,
            tryFindItemCheckboxesPopup,
            popups,
            rowCategories,
            rowCategoriesNode,
            rowCategoriesSwap;

        console.log(rows);

        for(let i in rows){
            rowCategoriesNode = rows[i].node.querySelector('[name=CATEGORY]');
            console.log(rowCategoriesNode);
            rowCategories = JSON.parse(rowCategoriesNode.dataset.items);
            rowCategoriesSwap = rowCategories;

            for(let j in window.constsCategories){
                tryFindItemHolder = rowCategoriesNode.querySelector("[data-item^='{\"VALUE\":\""+window.constsCategories[j]+"\"']");
                if(tryFindItemHolder)
                    tryFindItemHolder.querySelector('.main-ui-square-delete').remove();
            }

            rowCategoriesNode.addEventListener('click', (evt) => {
                setTimeout(() => {
                    popups = BX.Main.PopupWindowManager._popups;

                    tryFindItemCheckboxesPopupIndex = popups.findIndex(x => x.bindElement.classList == evt.target.closest('.main-grid-editor').classList);
                    tryFindItemCheckboxesPopup = popups[tryFindItemCheckboxesPopupIndex];
                    if(!tryFindItemCheckboxesPopup)
                        return;

                    for(let j in window.constsCategories){
                        tryFindItemHolder = tryFindItemCheckboxesPopup.popupContainer.querySelector("[data-item^='{\"VALUE\":\""+window.constsCategories[j]+"\"']");
                        if(tryFindItemHolder){
                            tryFindItemHolder.hidden=true;
                        }
                    }

                }, 50)
            })
        }
    }

}

BX.addCustomEvent('Grid::ready', BX.delegate(function (data) {
    window.PlayersJSClass = new someDev7Js123(data);
    if(document.querySelector('.main-grid-bottom-panels'))
        document.querySelector('.main-grid-bottom-panels').removeAttribute('hidden')
}));

BX.addCustomEvent('Grid::thereEditedRows', BX.delegate(function () {
    window.PlayersJSClass.onGridEditApply();
}));

BX.addCustomEvent('BX.Main.Filter:apply', BX.delegate(function (command, params) {
    window.PlayersJSClass.refreshGrid();
}));


Object.filter = (obj, predicate) =>
    Object.keys(obj)
        .filter( key => predicate(obj[key]) )
        .reduce( (res, key) => (res[key] = obj[key], res), {} );

function openSidePanel(props){
    BX.SidePanel.Instance.open(
        props.link,
        {
            requestMethod: "post",
            animationDuration: 100,
            cacheable: false,
            width: 1600,
        }
    );
}

function runCoolGridAction(action){
    switch (action) {
        case 'addRow':
            window.PlayersJSClass.createGridRow();
            window.PlayersJSClass.onGridAddApply();
            break;

        case 'removeTemplateItems':
            window.PlayersJSClass.removeGridRowTemplates();
            break;

        case 'save':
            window.PlayersJSClass.saveGridRows();
            break;

        case 'delete':
            window.PlayersJSClass.deleteGridRows();
            break;

        case 'showPanel':
            window.PlayersJSClass.getGrid().enableActionsPanel();
            break;
        case 'hidePanel':
            window.PlayersJSClass.getGrid().disableActionsPanel();
            break;

        case 'checkCorrectOanelShow':
            if(window.PlayersJSClass.getGridSelectedIds().length==0){
                window.PlayersJSClass.getGrid().disableActionsPanel();
            }
            break;




        default:
            console.log('partisan action: '+action);
            break;
    }
}

function deleteEntityElements(entityType, entityId, itemIds){
    alert('Удаление сущности ' + entityType + ' с id ' + entityId +'. Список ID элементов: ' + itemIds.join(', '));
}

let originalBxOnCustomEvent = BX.onCustomEvent;