BX.ready(function(){
    let descriptionToggleBtn = document.querySelector('.toggleHeight');
    let descriptionContent = document.querySelector('.project-description');

    descriptionContent.setAttribute('style', 'overflow:visible');
    if(isOverflown(descriptionContent))
        descriptionToggleBtn.hidden = false;
    descriptionContent.setAttribute('style', 'overflow:hidden');
});