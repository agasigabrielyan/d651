function showParent(id){
    let link = document.querySelector('#'+id);
    if(!link)
        return;

    link.scrollIntoView({
        block: 'start',
        behavior: 'smooth',
        top : 100,
    });

    setTimeout(function(){
        link.classList.add('light2')
    }, 200)

    setTimeout(function(){
        link.classList.remove('light2');
    }, 5000)

}