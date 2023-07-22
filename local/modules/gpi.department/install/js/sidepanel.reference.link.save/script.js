function setPreviusUrl(){
    let prevLocation = document.referrer.replace(location.origin, "");

    history.pushState({ foo: 'bar' }, '', prevLocation);
}


BX.ready(function(){

    if(BX.SidePanel.Instance.opened)
        setPreviusUrl();

})