BX.ready(function(){
    let formSelects = Array.from(document.querySelector('.cool-form').querySelectorAll('select'));

    for(let i in formSelects){
        $("#"+formSelects[i].getAttribute('id')).selectize();
    }
});

function addRequest(){
    let formData = {};

    let formEls = Array.from(document.querySelector('.cool-form').querySelectorAll('.form-target'));
    let requiredMiss=[];

    for(let i in formEls){
        if(formEls[i].getAttribute('id') == null)
            continue;

        if(formEls[i].required && !formEls[i].value)
            requiredMiss.push(formEls[i]);

        formData[formEls[i].getAttribute('id')] = formEls[i].value;
    }

    for(let i in requiredMiss){
        let container = requiredMiss[i].closest('legend');
        if(container == null)
            continue;
        container.classList.add('error');

        container.querySelector('.form-target').addEventListener('change', function(event) {

            if(event.target.closest('legend') == null)
                return false;

            event.target.closest('legend').classList.remove('error');
        })
    }

    if(requiredMiss.length>0)
        return false;

    BX.ajax.runComponentAction('rs:support', 'addRequest', {
        mode: 'class',
        data: {
            data: formData
        },
    }).then(function (response) {
        if(!response.data){
            showNotyMessage("Произошла ошибка");
            return;
        }

        let answer = JSON.parse(response.data);

        if(answer.status == 1){
            showNotyMessage("Обращение отправлено", 'BX.SidePanel.Instance.closeAll();');
            let formEls = Array.from(document.querySelector('.cool-form').querySelectorAll('.form-target'));

            for(let i in formEls){
                formEls[i].value='';
            }

        }else{
            showNotyMessage("Произошла ошибка: "+answer.error);
        }

    }, function (response) {
        console.log(response);
    });

}

function removeCustomDevEvents(event){
    event.target.removeEventListener('')
}