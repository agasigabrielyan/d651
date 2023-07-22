function saveConfig(){
    window.entityUserPermissionConfigurator.save();
    BX.ajax.runComponentAction('rs:activity.directions.settings', 'syncDirectionAdsPermissions', {
        mode: 'class',
        data:{
            true: true
        },
    }).then(function (response) {

    }, function (response) {

    });
}