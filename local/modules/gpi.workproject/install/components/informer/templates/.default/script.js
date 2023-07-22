function setCompactBlockContent(content, targetBody){

    let menu = document.querySelector('.light-menu');

    if(targetBody.innerHTML.trim() !=''){
        targetBody.classList.add('active');
        menu.classList.remove('disabled');
        return;
    }

    const bxFormData = new BX.ajax.FormData();

    bxFormData.append('content', content);

    bxFormData.send(
        '/bitrix/services/main/ajax.php?mode=class&c=rs:informer&action=setCompactBlockContent&sessid='+BX.bitrix_sessid(),
        function (response)
        {
            targetBody.innerHTML = JSON.parse(response).data;
            targetBody.classList.add('active');
            menu.classList.remove('disabled');
        },
        null,
        function(error)
        {

        }
    );
}

BX.ready(function(){
    let marker = document.querySelector('#marker');
    let list = Array.from(document.querySelectorAll('.light-menu li'));
    let targets = Array.from(document.querySelectorAll('.targets .target'));

    for(let i in list){
        list[i].addEventListener('click', (e) => {
            moveIndicartor(e.target);
        })
        list[i].addEventListener('click', activeLink)
    }

    if(document.querySelector('.light-menu li[data-link="'+window.active_informer+'"]'))
        document.querySelector('.light-menu li[data-link="'+window.active_informer+'"]').click();

    function moveIndicartor(e){
        let menu = document.querySelector('.light-menu');

        if(menu.classList.contains('disabled'))
            return;

        marker.style.left = e.offsetLeft+'px';
        marker.style.width = e.offsetWidth+'px';
    }

    function activeLink(){

        let menu = document.querySelector('.light-menu');

        if(menu.classList.contains('disabled'))
            return;

        menu.classList.add('disabled');

        for (let i in list)
            list[i].classList.remove('active');

        for (let i in targets)
            targets[i].classList.remove('active');

        this.classList.add('active');
        let targetBody = document.querySelector('#'+this.getAttribute('data-link'));

        if(targetBody.innerHTML.trim()=='' || 1==1){
            setCompactBlockContent(this.getAttribute('data-link'), targetBody);
        }else
            targetBody.classList.add('active');

    }
})