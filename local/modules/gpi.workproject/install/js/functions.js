function openSidePanel(link, width = 1600, script, data={}){
    window.boxOnOkScript = script;
    let side = BX.SidePanel.Instance.open(
        link,
        {
            requestMethod: "post",
            animationDuration: 100,
            cacheable: false,
            width: width,
            data:data,
            events: {
                onCloseComplete: function(event) {

                    if(boxOnOkScript){
                        eval(boxOnOkScript);
                    }
                },
            }
        }
    );

}

function showNotyMessage(message, script){
    window.boxOnOkScript = script;
    let box = new BX.UI.Dialogs.MessageBox({
        message: BX.create({
            tag : 'div',
            attrs : { style : 'font: 17px "OpenSans", "Helvetica Neue", Helvetica, Arial, sans-serif;text-align: center;'},
            html : message,
        }),
        buttons: BX.UI.Dialogs.MessageBoxButtons.OK,
        okCaption: "ОК",
        onOk : function (messageBox){
            messageBox.close();
            if(window.boxOnOkScript){
                eval(window.boxOnOkScript);
            }
        },
        modal: true,
    })

    box.show();
    return box;
}

async function sendWorkForm(form, fields, componentName, actionName, evalAfterScript){
    let formEls = fields
        ,data = BX.ajax.prepareForm(form).data
        ,formData = {}
        ,self = this
        ,requiredMiss=[];


    for(let i in formEls){

        if(formEls[i].getAttribute('name') == null)
            continue;

        if(formEls[i].required && !formEls[i].value && !formEls[i].closest('.hidden'))
            requiredMiss.push(formEls[i]);
    }

    for(let i in requiredMiss){
        let container = requiredMiss[i].closest('legend');

        if(container == null)
            continue;
        container.classList.add('error');

        container.querySelector('.field').addEventListener('change', function(event) {

            let target = event.target.closest('legend');

            if(target == null)
                return true;

            target.classList.remove('error');
        })
    }

    if(requiredMiss.length>0)
        return false;

    let filesArs = Array.from(form.querySelectorAll('input[type=file]'));
    let filesPostArs = [];
    let name;

    for(let i in filesArs){

        name = filesArs[i].getAttribute('name');

        for(let index in filesArs[i].files)
        {
            data[name+index] = filesArs[i].files[index];
        }

        data[name+'_IS_MULTIPLE'] = filesArs[i].multiple;

        let filesPostArs = Array.from(filesArs[i].closest('.inputFiles').querySelectorAll('.inputFilesList__past  .inputFilesList__row'));
        data[name+'_POST_LIST'] = [];
        for(let j in filesPostArs){
            data[name+'_POST_LIST'].push(filesPostArs[j].dataset.fileId);
        }
    }

    const bxFormData = new BX.ajax.FormData();

    let selectElements = Array.from(form.querySelectorAll('select[multiple]'));
    let selects;
    for(let i in selectElements){
        selects = [];
        for (let j in selectElements[i].options) {
            if (selectElements[i].options[j].selected) {
                selects.push(selectElements[i].options[j].value);
            }
        }
        data[selectElements[i].getAttribute('name')] = selects;
    }

    for(let name in data)
    {
        bxFormData.append(name, data[name]);
    }

    let result = await bxFormData.send(
        '/bitrix/services/main/ajax.php?mode=class&c='+componentName+'&action='+actionName+'&sessid='+BX.bitrix_sessid(),
        function (response)
        {
            response = JSON.parse(response);

            if(response.status === 'success')
            {
                eval(evalAfterScript);
            }
            else
            {
                showNotyMessage("Ошибка сохранения")
            }
        },
        null,
        function(error)
        {
            //showNotyMessage("Ошибка сохранения2")
        }
    );

    return result;
}

function isOverflown(element) {
    return element.scrollHeight > element.clientHeight || element.scrollWidth > element.clientWidth;
}