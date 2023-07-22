function refreshAlbumsContent(){
    BX.ajax({
        url: window.location.href,
        method: 'POST',
        data: {
            GRID_ID : window.rs_albums_grid_id,
        },
        processData: true,
        onsuccess: function(data){

            let parser = new DOMParser();
            let doc = parser.parseFromString(data, 'text/html');
            document.querySelector('.albums-list').innerHTML = doc.querySelector('.albums-list').innerHTML;
            window.albumsEditor.buildEditorBodies();
        },
        onfailure: function(e){
            console.log(e);
        }
    });
}

function createAlbum(){


    let box = new BX.UI.Dialogs.MessageBox({
        title : 'Создание альбома',
        message: BX.create({
            tag : 'div',
            attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
            html : '' +
                '<form class="createFolder">' +
                    '<input type="hidden" name="GALLERY_ID" class="form-target" value="'+window.rsGalleryParams.GALLERY_ID+'" >' +
                    '<legend>' +
                    '   <label for="TITLE">Название*</label>' +
                    '   <input class="form-target" type="text" id="TITLE" name="TITLE">' +
                    '</legend>' +
                    '<legend>' +
                    '   <label for="DESCRIPTION">Описание*</label>' +
                    '   <textarea class="form-target" type="text" id="DESCRIPTION" name="DESCRIPTION"></textarea>'+
                    '</legend>' +
                    '<legend>' +
                    '   <label for="PREVIEW">Превью*</label> ' +
                    '   <input type="file" name="PREVIEW"> ' +
                    '</legend>' +
                '</form>',
        }),
        buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
        okCaption: "Создать",
        onOk: function(messageBox)
        {
            window.galleryMessageBox = messageBox;
            sendWorkForm(
                messageBox.popupWindow.contentContainer.querySelector('form'),
                Array.from(messageBox.popupWindow.contentContainer.querySelectorAll('.form-target')),
                'rs:gallery.albums',
                'loadEntity',
                'window.galleryMessageBox.close();refreshAlbumsContent();'
            );
        },
        modal: true,
    })

    box.show();

    new BearFileInput(box.popupWindow.contentContainer.querySelector('input[type=file]'), []);


    return box;
}

function editAlbum(album){
    let box = new BX.UI.Dialogs.MessageBox({
        title : 'Редактирование альбома',
        message: BX.create({
            tag : 'div',
            attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
            html :
                '<form class="createFolder">' +
                    '<input type="hidden" name="GALLERY_ID" class="form-target" value="'+window.rsGalleryParams.GALLERY_ID+'"> ' +
                    '<input type="hidden" name="ID" class="form-target" value="'+album.ID+'"> ' +
                    '<legend>' +
                    '   <label for="TITLE">Название*</label>' +
                    '   <input class="form-target" type="text" value="'+album.TITLE+'" id="TITLE" name="TITLE">' +
                    '</legend>' +
                    '<legend>' +
                    '   <label for="DESCRIPTION">Описание*</label>' +
                    '   <textarea class="form-target" type="text" id="DESCRIPTION" name="DESCRIPTION">' +album.DESCRIPTION+'</textarea>'+
                    '</legend>' +
                    '<legend>' +
                    '   <label for="PREVIEW">Превью*</label> ' +
                    '   <input type="file" name="PREVIEW"> ' +
                    '</legend>' +
                '</form>',
        }),
        buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
        okCaption: "Сохранить",
        onOk: function(messageBox)
        {

            window.galleryMessageBox = messageBox;
            sendWorkForm(
                messageBox.popupWindow.contentContainer.querySelector('form'),
                Array.from(messageBox.popupWindow.contentContainer.querySelectorAll('.form-target')),
                'rs:gallery.albums',
                'loadEntity',
                'window.galleryMessageBox.close();refreshAlbumsContent();'
            );

        },
        modal: true,
    })

    box.show();

    new BearFileInput(box.popupWindow.contentContainer.querySelector('input[type=file]'), [album], 'PREVIEW_FILE_');
}

function showConfirmToDeleteAlbum(id){

    let box = new BX.UI.Dialogs.MessageBox({
        message: BX.create({
            tag : 'div',
            attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
            html : 'Подтвердите удаление альбома',
        }),
        buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
        okCaption: "ОК",
        onOk: function(messageBox)
        {
            BX.ajax.runComponentAction('rs:gallery.albums', 'deleteEntity', {
                mode: 'class',
                data: {
                    id: id,
                },
            }).then(function (response) {
                showNotyMessage("Успешно");
                messageBox.close();
                refreshAlbumsContent();
            }, function (response) {
                showNotyMessage("Произошла ошибка");
            });
        },
        modal: true,
    })

    box.show();

}