function setTargetPosition(evt){
    let target = document.querySelector('.'+evt.target.getAttribute('for')),
        targets = Array.from(document.querySelectorAll('.magic-target')),
        disableEl = evt.target.parentNode.querySelector('.disable-el');

    for(let i in targets){
        targets[i].style.order=2;
        targets[i].setAttribute('style', 'position: absolute!important;');
    }
    target.style.order=1;
    disableEl.classList.add('show');

    

    setTimeout(() => {
        disableEl.classList.remove('show');
        for(let i in targets){
            targets[i].setAttribute('style', 'position: absolute!important;');
        }
        target.setAttribute('style', 'position: static!important;');
        
    },1000)
}