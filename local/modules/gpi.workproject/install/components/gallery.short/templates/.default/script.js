

BX.ready(function(){

    $('.galleryimg').owlCarousel({
        loop:false,
        items:3,
        autoWidth: false,
        nav:false,
        dots : false,
    })

    document.querySelector('.gallery').querySelector('.customServicesOwlNext').addEventListener('click', function (){
        $('.galleryimg').trigger('next.owl.carousel', [300]);
    })

    document.querySelector('.gallery').querySelector('.customServicesOwlPrev').addEventListener('click', function (){
        $('.galleryimg').trigger('prev.owl.carousel', [300]);
    })
    
    let galleryWrapper = document.querySelector('.galleryimg');

    if(galleryWrapper){
        let galleryWrapperWidth = galleryWrapper.getBoundingClientRect().width;
        let quiteWidth = (galleryWrapperWidth/3)-20;

        let photos = Array.from(galleryWrapper.querySelectorAll('img'));

        for(let i in photos){
            photos[i].style.width = quiteWidth+'px';
        }

    }
})