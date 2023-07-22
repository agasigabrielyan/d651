function saveConfig(){

    let storageInput = document.querySelector('[name=STORAGE_ID]'),
        titleInput = document.querySelector('[name=TITLE]');

    if(!storageInput.value){
        showNotyMessage('Неопознанный диск');
        return;
    }

    if(!titleInput.value){
        showNotyMessage('Введите название диска');
        return;
    }

    BX.ajax.runComponentAction('rs:storage.items', 'renameStorage', {
        mode: 'class',
        data: {
            id: storageInput.value,
            title : titleInput.value,
        },
    }).then(function (response) {
        window.entityUserPermissionConfigurator.save();
    }, function (response) {
        console.log(response);
    });
}