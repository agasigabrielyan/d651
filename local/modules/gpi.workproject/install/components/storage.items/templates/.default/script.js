class someDev7JsDrive
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
        return window.SomeJsDriveClass.getGrid().getActionsPanel().getSelectedIds();
    }

    getGridSellectedRows(){
        let elements = Array.from(this.getWorkarea().querySelectorAll('.main-grid-row-checked.main-grid-row-body'));
        let result = [];
        for(let i in elements){
            if(elements[i].classList.contains('main-grid-not-count'))
                continue;

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
        return window.SomeJsDriveClass.getGrid().getRowEditorValue();
    }



    createGridRow(){
        this.getGrid().appendRowEditor();

        let addRowsData = this.getGridTemplateRows();
    }

    saveGridRows(){
        let self = this;
        let saveRowsData = this.getGridSellectedValues();

        let addRowsData = this.getGridTemplateRowsValues();

        BX.ajax.runComponentAction('rs:storage.items', 'sendObjectList', {
            mode: 'class',
            data: {
                update : saveRowsData,
            }
        }).then(function (response) {
            self.refreshGrid();
        });
    }

    removeGridRowTemplates(){
        let templates = this.getGridTemplateRows();
        for(let i in templates){
            templates[i].node.remove();
        }
    }

    deleteGridRows(){
        let self = this;

        BX.ajax.runComponentAction('rs:storage.items', 'deleteObjects', {
            mode: 'class',
            data: {
                delete : window.SomeJsDriveClass.getGridSelectedIds(),
            }
        }).then(function (response) {
            self.refreshGrid();
        });

    }

    refreshGrid(){
        let workarea = this.getWorkarea();
        let self = this;

        window.SomeJsDriveClass.getGrid().getLoader().show();

        BX.ajax({
            url: window.drivePath,
            data :{
                GRID_ID : window.driveGridId
            },
            method: 'POST',
            processData: true,
            onsuccess: function(data){

                window.SomeJsDriveClass.getGrid().getLoader().hide();

                let parser = new DOMParser();
                let doc = parser.parseFromString(data, 'text/html');
                document.querySelector('.drive-container').innerHTML = doc.querySelector('.drive-container').innerHTML;

            },
            onfailure: function(e){
                console.log(e);
            }
        });
    }



    onGridEditApply(){
        this.setAllRowsCustomFieldsEditCont();
    }


    setAllRowsCustomFieldsEditCont(){

        let columns = this.getGridColums();
        let customColumns = Object.filter(columns, x => x.type == 'CUSTOM');

        if(!customColumns)
            return true;

        let rows = this.getGridSellectedRows();

        for (let i in rows){
            let row = rows[i];
            this.setRowNodeCustomFieldsEditCont(row.node, customColumns);
        }
    }

    setRowNodeCustomFieldsEditCont(){
    }

    createFolder(){

        let form=[];

        form.push('<input hidden type="text" name="STORAGE_ID" value="'+window.storageId+'">');
        if(window.parentId)
            form.push('<input hidden type="text" name="PARENT_ID" value="'+window.parentId+'">');
        form.push('<legend><label for="TITLE">Название*</label><input type="text" id="TITLE" name="TITLE"></legend>');

        form = '<form class="createFolder">'+form.join('')+'</form>'

        let box = new BX.UI.Dialogs.MessageBox({
            title : 'Создание папки',
            message: BX.create({
                tag : 'div',
                attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
                html : form,
            }),
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
            okCaption: "Создать",
            onOk: function(messageBox)
            {

                let titleInput = messageBox.popupWindow.contentContainer.querySelector('[name=TITLE]');

                if(!titleInput.value){
                    messageBox.close();
                    box = createFolder();
                    console.log(box);
                    titleInput = box.popupWindow.contentContainer.querySelector('[name=TITLE]');
                    titleInput.style.border = '1px solid red';
                    return false;
                }

                BX.ajax.runComponentAction('rs:storage.items', 'createFolder', {
                    mode: 'class',
                    data: {
                        title: titleInput.value,
                        storageId: window.storageId,
                        parentId: window.parentId ?? '',
                    },
                }).then(function (response) {
                    showNotyMessage("Успешно");
                    messageBox.close();
                    window.SomeJsDriveClass.refreshGrid();
                }, function (response) {
                    showNotyMessage("Произошла ошибка");
                });
            },
            modal: true,
        })

        box.show();

        return box;
    }

    createFile(){

        let form=[];

        form.push('<input hidden type="text" name="STORAGE_ID" value="'+window.storageId+'">');
        if(window.parentId)
            form.push('<input hidden type="text" name="PARENT_ID" value="'+window.parentId+'">');
        form.push('<legend><label for="FILES">Файлы*</label><input type="file" id="FILES" multiple name="FILES"></legend>');

        form = '<form>'+form.join('')+'</form>'


        let box = new BX.UI.Dialogs.MessageBox({
            title : 'Загрузка файлов',
            message: BX.create({
                tag : 'div',
                attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
                html : form,
            }),
            buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
            okCaption: "Загрузить",
            onOk: function(messageBox)
            {

                let myForm = messageBox.popupWindow.contentContainer.querySelector("form");
                let data = BX.ajax.prepareForm(myForm).data;

                let files = myForm.querySelector('.uploadFiles');

                if(files)
                    for(let index in files.files)
                    {
                        data['file'+index] = files.files[index];
                    }

                const bxFormData = new BX.ajax.FormData();

                for(let name in data)
                {
                    bxFormData.append(name, data[name]);
                }

                bxFormData.send(
                    '/bitrix/services/main/ajax.php?mode=class&c=rs:storage.items&action=loadFiles&sessid='+BX.bitrix_sessid(),
                    function (response)
                    {
                        response = JSON.parse(response);

                        if(response.status === 'success')
                        {
                            showNotyMessage("Успешно");
                            messageBox.close();
                            window.SomeJsDriveClass.refreshGrid();
                        }
                        else
                        {
                            showNotyMessage("Ошибка сохранения")
                        }
                    },
                    null,
                    function(error)
                    {
                        showNotyMessage("Ошибка сохранения")
                    }
                );

            },
            modal: true,
        })

        box.show();

        new BearFileInput(box.popupWindow.contentContainer.querySelector('input[type=file]'));
    }


    openFolder(link){

        location.href = link;

        window.SomeJsDriveClass.getGrid().getLoader().show();
        BX.ajax({
            url: link,
            data :{
                GRID_ID : window.driveGridId,
            },
            method: 'POST',
            processData: true,
            onsuccess: function(data){
                window.SomeJsDriveClass.getGrid().getLoader().hide();
                let parser = new DOMParser();
                let doc = parser.parseFromString(data, 'text/html');
                document.querySelector('.drive-container').innerHTML = doc.querySelector('.drive-container').innerHTML;

                window.drivePath = link;
                console.log(window.drivePath);
            },
            onfailure: function(e){
                console.log(e);
            }
        });
    }

}

BX.addCustomEvent('Grid::ready', BX.delegate(function (data) {
    if(data.containerId == window.driveGridId)
        window.SomeJsDriveClass = new someDev7JsDrive(data);

}));

BX.addCustomEvent('Grid::thereEditedRows', BX.delegate(function (data) {
    window.SomeJsDriveClass.onGridEditApply();
}));

BX.addCustomEvent('BX.Main.Filter:apply', BX.delegate(function (command, params) {
    window.SomeJsDriveClass.refreshGrid();
}));


Object.filter = (obj, predicate) =>
    Object.keys(obj)
        .filter( key => predicate(obj[key]) )
        .reduce( (res, key) => (res[key] = obj[key], res), {} );



function runCoolGridDriveAction(action){
    switch (action) {
        case 'addRow':
            window.SomeJsDriveClass.createGridRow();
            break;

        case 'removeTemplateItems':
            window.SomeJsDriveClass.removeGridRowTemplates();
            break;

        case 'save':
            window.SomeJsDriveClass.saveGridRows();
            break;

        case 'delete':
            window.SomeJsDriveClass.deleteGridRows();
            break;

        case 'showPanel':
            window.SomeJsDriveClass.getGrid().enableActionsPanel();
            break;
        case 'hidePanel':
            window.SomeJsDriveClass.getGrid().disableActionsPanel();
            break;

        case 'checkCorrectOanelShow':
            if(window.SomeJsDriveClass.getGridSelectedIds().length==0){
                window.SomeJsDriveClass.getGrid().disableActionsPanel();
            }
            break;




        default:
            console.log('partisan action: '+action);
            break;
    }
}