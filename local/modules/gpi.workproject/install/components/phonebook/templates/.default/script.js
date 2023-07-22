function getContactRequset(el){
    if(el.value.length > 2){
        getPhonebookdata(el.value, el);
    }else{
        let resultBlock = el.closest('.phonebook').querySelector('.phonebook-result');
        let ghostBlock = el.closest('.phonebook').querySelector('.goost-result');
            

        ghostBlock.removeAttribute('hidden');
        resultBlock.setAttribute('hidden', true);

    }

    if(el.value.length>0)
        el.closest('.block-title').querySelector('.pencil').classList.add('writed');
    else
        el.closest('.block-title').querySelector('.pencil').classList.remove('writed');
}

function getPhonebookdata(likeText, el){

    BX.ajax({
        url: 'https://www1.adm.gazprom.ru/Phones_pre/PhoneWebService.asmx/GetEmployeesByFIO',
        method: 'POST',
        data: {
            fio : likeText,
            count: 20,
        },
        processData: true,
        onsuccess: function(data){
            let usersList; 
            let resultBlock = el.closest('.phonebook').querySelector('.phonebook-result');
            let ghostBlock = el.closest('.phonebook').querySelector('.goost-result');
            let responseUsers = JSON.parse(data);

            ghostBlock.setAttribute('hidden', true);
            resultBlock.removeAttribute('hidden');
            resultBlock.innerHTML = '';
            let header = BX.create({
                tag: 'div',
                attrs: {className: 'row'},
                children : [
                    BX.create({
                        tag: 'div',
                        attrs: {className: 'col-4'},
                        text:'Фио',
                    }),
                    BX.create({
                        tag: 'div',
                        attrs: {className: 'col-2'},
                        text: 'Номер',
                    }),
                    BX.create({
                        tag: 'div',
                        attrs: {className: 'col-3'},
                        text: 'Должность',
                    }),
                    BX.create({
                        tag: 'div',
                        attrs: {className: 'col-3'},
                        text: 'Подразделение',
                    })
                ]
            });
            resultBlock.appendChild(header);

            if(responseUsers.length=='undefined' || responseUsers.length==0){
                resultBlock.appendChild(BX.create({
                    tag: 'div',
                    attrs: {className: 'row'},
                    children : [
                        BX.create({
                            tag: 'div',
                            attrs: {className: 'col-12 nothing'},
                            text: 'Ничего не найдено',
                        }),
                    ]
                }));
                return false;
            }

            for(let i in responseUsers){
                let user = responseUsers[i];
                let row = BX.create({
                    tag: 'div',
                    attrs: {className: 'row'},
                    children : [
                        BX.create({
                            tag: 'div',
                            attrs: {className: 'col-4'},
                            html: user.LastName+' '+user.FirstName+' '+user.MiddleName,
                        }),
                        BX.create({
                            tag: 'div',
                            attrs: {className: 'col-2'},
                            html: user.Phone,
                        }),
                        BX.create({
                            tag: 'div',
                            attrs: {className: 'col-3'},
                            html: user.Position,
                        }),
                        BX.create({
                            tag: 'div',
                            attrs: {className: 'col-3'},
                            html: user.Department,
                        })
                    ]
                });

                resultBlock.appendChild(row);
            }
            
        },
    });
}


/*

    

function getPhonebookdata(likeText){
    BX.ajax.runComponentAction('rs:phonebook', 'getLikeContects', {
        mode: 'class',
        data: {
            fio : likeText,
            count: 20,
        },
    }).then(function (response) {
        
    }, function (response) {
        console.log(response);
    });
}*/
