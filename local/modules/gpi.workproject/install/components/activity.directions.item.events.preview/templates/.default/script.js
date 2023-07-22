BX.ready(function(){
    let photos = Array.from(document.querySelector('.activity_direction').querySelectorAll('.background-picture'));

    for(let i in photos){
        let phWidth = photos[i].getBoundingClientRect().width;

        let maxHeight = phWidth/1920*1080;
        photos[i].style.maxHeight = 233+"px";
    }
})