BX.ready(function(){

    let captionsList = document.querySelectorAll('.caption-item');
    let unitsList = document.querySelectorAll('.unit');

    captionsList.forEach(function (item, index) {
        item.addEventListener('mouseover', function () {

            if(unitsList[index]){
                unitsList[index].classList.add('hovered');
                let usage = document.querySelector('.activity-direction-index').querySelector('.slink');
                usage.setAttribute('xlink:href', '#circle'+ unitsList[index].getAttribute('data-id'));
            }
        });

        item.addEventListener('mouseout', function () {
            if(unitsList[index]){
                unitsList[index].classList.remove('hovered');
            }
        });
    });
})

function showTargetLinksD7(target, save=true){
    let targets = Array.from(document.querySelector('.desk-block-list').querySelectorAll('li'));

    let items = targets.filter(el => el.getAttribute('data-link-id') == target.getAttribute('data-id'));

    let top = 50;
    for (let i in targets){
        if(!targets[i].classList.contains('hidden')){
            targets[i].style.top = top+'px';
            top=top+targets[i].getBoundingClientRect().height;
            targets[i].classList.add('hidden');
        }
    }

    for (let i in items){
        items[i].classList.remove('hidden')
    }

    let usage = document.querySelector('.activity-direction-index').querySelector('.slink');
    usage.setAttribute('xlink:href', '#circle'+ target.getAttribute('data-id'));

    let links = Array.from(document.querySelector('.activity-direction-index').querySelectorAll('.caption-item'));

    for (let i in links){
        if(links[i].getAttribute('data-id') != target.getAttribute('data-id'))
            links[i].classList.remove('active');
        else
            links[i].classList.add('active');
    }

    let links2 = Array.from(document.querySelector('.activity-direction-index').querySelectorAll('.unit'));

    for (let i in links2){
        if(links2[i].getAttribute('data-id') != target.getAttribute('data-id'))
            links2[i].classList.remove('active');
        else
            links2[i].classList.add('active');
    }

    document.querySelector('.activity-text').innerText = target.dataset.curator;

    if(!save)
        return;


    BX.setCookie('activeDirection', target.getAttribute('data-id'), {expires: 86400});
}

function createDetailDirectionLink(linkPathern, id){
    let mainId = BX.getCookie('activeDirection');
    if(!mainId){
        mainId = id;
    }

    openSidePanel(linkPathern.replace('#main_id#', mainId), 600, 'window.coolEditor.refreshContent();');
}
