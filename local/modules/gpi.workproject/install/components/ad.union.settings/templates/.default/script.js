function saveConfig(){

    let adUnion = document.querySelector('[name=UNION_ID]'),
        titleInput = document.querySelector('[name=TITLE]');

    if(!adUnion.value){
        showNotyMessage('Неопознанные события');
        return;
    }

    if(!titleInput.value){
        showNotyMessage('Введите название событий');
        return;
    }

    BX.ajax.runComponentAction('rs:ad.union.list', 'renameAdUnion', {
        mode: 'class',
        data: {
            id: adUnion.value,
            title : titleInput.value,
        },
    }).then(function (response) {
        window.entityUserPermissionConfigurator.save();
    }, function (response) {
        console.log(response);
    });
}