function editDepartment(){
    BX.SidePanel.Instance.open("rs:department", {
        contentCallback: function(slider){
            return new Promise(function(resolve, reject){
                BX.ajax.runComponentAction('rs:department', 'getEditComponentResult', {
                    mode: 'class',
                }).then(function(response){
                    if(!window.editJSEnabled){
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(response.data.strings, 'text/html');
                        console.log(doc);
                    }

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
