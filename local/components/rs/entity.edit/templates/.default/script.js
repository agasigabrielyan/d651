function loadForm(){
    sendWorkForm(
        document.querySelector('.entity-edit-form'),
        Array.from(document.querySelector('.entity-edit-form').querySelectorAll('.field')),
        'rs:entity.edit',
        'loadEntity',
        'BX.SidePanel.Instance.close();'
    );

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