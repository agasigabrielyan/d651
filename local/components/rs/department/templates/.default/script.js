function editDepartment(){
    BX.SidePanel.Instance.open("rs:department", {
        contentCallback: function(slider){
            return new Promise(function(resolve, reject){
                BX.ajax.runComponentAction('rs:department', 'getEditComponentResult', {
                    mode: 'class',
                }).then(function(response){
                    if(!window.editJSEnabled)
                        document.head.insertAdjacentHTML('beforeend', response.data.strings);

                    window.editJSEnabled=1;
                    resolve({html: response.data.content});
                })
            });
        },
        animationDuration: 100,
        width: 700,
        cacheable: false,
    });
}
