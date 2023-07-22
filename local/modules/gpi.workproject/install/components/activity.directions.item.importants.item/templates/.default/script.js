function reloadDirectionAdsContent(){
    BX.ajax({
        url: location.href,
        method: 'POST',
        data: {
            GRID_ID : window.direction_ads_grid_id,
        },
        processData: true,
        onsuccess: function(data){

            let parser = new DOMParser();
            let doc = parser.parseFromString(data, 'text/html');
            document.querySelector('.detal-element').innerHTML = doc.querySelector('.detal-element').innerHTML;
        },
        onfailure: function(e){
            console.log(e);
        }
    });
}