function saveConfig(){

    let calendarInput = document.querySelector('[name=CALENDAR_ID]'),
        titleInput = document.querySelector('[name=TITLE]');

    if(!calendarInput.value){
        showNotyMessage('Необознанный календарь');
        return;
    }

    if(!titleInput.value){
        showNotyMessage('Введите название календаря');
        return;
    }

    BX.ajax.runComponentAction('rs:calendar.events', 'renameCalendar', {
        mode: 'class',
        data: {
            id: calendarInput.value,
            title : titleInput.value,
        },
    }).then(function (response) {
        window.entityUserPermissionConfigurator.save();
    }, function (response) {
        console.log(response);
    });
}