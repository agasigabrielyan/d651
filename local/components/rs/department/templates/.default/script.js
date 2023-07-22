function editDepartment(){
    BX.SidePanel.Instance.open("rs:department", {
        contentCallback: function(slider){
            return new Promise(function(resolve, reject){
                BX.ajax.runComponentAction('rs:department', 'getEditComponentResult', {
                    mode: 'class',
                }).then(function(response){
                    if(!window.editJSEnabled){
                        for(let i in response.data.strings.styles)
                            document.head.append(BX.create({
                                tag: 'link',
                                attrs: {type:'text/css', rel:'stylesheet', href: response.data.strings.styles[i]}
                            }))
                        for(let i in response.data.strings.scripts)
                            document.head.append(BX.create({
                                tag: 'script',
                                attrs: {type:'text/javascript', src: response.data.strings.scripts[i]}
                            }))
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
