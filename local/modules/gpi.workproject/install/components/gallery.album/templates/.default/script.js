function refreshAlbumContent(){
    BX.ajax({
        url: window.location.href,
        method: 'POST',
        data: {
            GRID_ID : window.rs_album_grid_id,
        },
        processData: true,
        onsuccess: function(data){

            let parser = new DOMParser();
            let doc = parser.parseFromString(data, 'text/html');
            document.querySelector('.album').innerHTML = doc.querySelector('.album').innerHTML;
            window.albumsItemEditor.buildEditorBodies();
        },
        onfailure: function(e){
            console.log(e);
        }
    });
}


function loadPhotos(){

    let box = new BX.UI.Dialogs.MessageBox({
        title : 'Загрузка фотографий',
        message: BX.create({
            tag : 'div',
            attrs : { style : 'font: 14px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
            html : '<form><input name="ALBUM_ID" id="ALBUM_ID" value="'+window.album_id+'" hidden><legend><input type="file" class="uploadFiles" multiple name="FILES"></legend></form>',
        }),
        buttons: BX.UI.Dialogs.MessageBoxButtons.OK_CANCEL,
        okCaption: "Загрузить",
        onOk: function(messageBox)
        {

            let myForm = messageBox.popupWindow.contentContainer.querySelector("form");
            let data = BX.ajax.prepareForm(myForm).data;

            let files = document.querySelector('.uploadFiles');
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

            if(!data['file0']){
                messageBox.close();
                return;
            }
                

            bxFormData.send(
                '/bitrix/services/main/ajax.php?mode=class&c=rs:gallery.album&action=loadPhotos&sessid='+BX.bitrix_sessid(),
                function (response)
                {
                    response = JSON.parse(response);

                    if(response.status === 'success')
                    {
                        showNotyMessage("Успешно");
                        messageBox.close();
                        refreshAlbumContent();
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

    new BearFileInput(box.popupWindow.contentContainer.querySelector('input[type=file]'), []);

}

function deletePicture(id, btn){
    BX.ajax.runComponentAction('rs:gallery.album', 'deletePhoto', {
        mode: 'class',
        data: {
            id: id,
        },
    }).then(function (response) {
        refreshAlbumContent();
    }, function (response) {
        showNotyMessage("Произошла ошибка");
    });
    btn.remove();
}