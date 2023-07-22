function saveConfig(){

    let forum = document.querySelector('[name=FORUM_ID]'),
        titleInput = document.querySelector('[name=TITLE]');

    if(!forum.value){
        showNotyMessage('Неопознанные события');
        return;
    }

    if(!titleInput.value){
        showNotyMessage('Введите название событий');
        return;
    }

    BX.ajax.runComponentAction('rs:forum.discussion.list', 'renameForum', {
        mode: 'class',
        data: {
            id: forum.value,
            title : titleInput.value,
        },
    }).then(function (response) {
        window.entityUserPermissionConfigurator.save();
    }, function (response) {
        console.log(response);
    });
}