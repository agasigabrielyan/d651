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
                        let headStyles = Array.from(doc.querySelectorAll('link'));
                        let headScripts = Array.from(doc.querySelectorAll('script'));

                        for (let i in headStyles)
                            document.head.appendChild(headStyles[i]);

                        for (let i in headScripts)
                            document.head.appendChild(headScripts[i]);
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
