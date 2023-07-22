function saveConfig(){

    let gallery = document.querySelector('[name=GALLERY_ID]'),
        titleInput = document.querySelector('[name=TITLE]');

    if(!gallery.value){
        showNotyMessage('Неопознанная галерея');
        return;
    }

    if(!titleInput.value){
        showNotyMessage('Введите название галереи');
        return;
    }

    BX.ajax.runComponentAction('rs:gallery.albums', 'renameGallery', {
        mode: 'class',
        data: {
            id: gallery.value,
            title : titleInput.value,
        },
    }).then(function (response) {
        window.entityUserPermissionConfigurator.save();
    }, function (response) {
        console.log(response);
    });
}