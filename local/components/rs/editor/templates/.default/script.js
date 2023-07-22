function loadForm(){
    sendWorkForm(
        document.querySelector('.entity-edit-form'),
        Array.from(document.querySelector('.entity-edit-form').querySelectorAll('.field')),
        'rs:editor',
        'loadEntity',
        'BX.SidePanel.Instance.close();'
    );

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
            response = JSON.parse(response).data;

            if(response.status)
            {
                eval(evalAfterScript);
            }
            else
            {
                showNotyMessage(response.error)
            }
        },
        null,
        function(error, error2, error3, error4, error5)
        {
            showNotyMessage("Ошибка сохранения")
        }
    );

    return result;
}


function addMultipleStringRow(el){

    let clon = el.closest('legend').querySelector('div'),
        input = clon.querySelector('input').value,
        swap = input.value;

    input.value='';

    clon.parentNode.insertBefore(BX.create({
        tag : 'div',
        html : clon.outerHTML,
    }), el.closest('legend').querySelector('.ui-btn').previousSibling);

    input.value = swap;
}

document.addEventListener('keypress', (evt) => {
    if(evt.keyCode == 13)
        loadForm();
})

BX.ready(() => {
    document.querySelector('.entity-edit-form').querySelector('input:not(hidden)').focus();
})
