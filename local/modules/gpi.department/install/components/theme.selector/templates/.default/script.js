BX.ready(function(){
    let selector = document.querySelector('.main-site-theme-selector-7311');
    if(selector != null) {
        selector.addEventListener('click', function (){
            showThemeeSelectorMenu();
        });
    }

    let selector2 = document.querySelector('.main-site-theme-selector-7310');
    if(selector != null) {
        selector2.addEventListener('click', function (e){
            switch(e.target.checked){
                case true:
                    setTheme('WHITE');
                break;

                case false:
                    setTheme('BLACK');
                break;
            }

        });
    }
});


function showThemeeSelectorMenu(){
    var bindElement = document.querySelector('.main-site-theme-selector-7311');

    BX.addClass(bindElement, "selector-active");
    BX.PopupMenu.show("main-site-theme-selector-7311", bindElement,
        [
            {
                text : "День",
                className : "menu-popup-no-icon"+ window.userSelectedTheme == 'WHITE' ? 'active' : '',
                onclick: function(e, item){
                    BX.PreventDefault(e);
                    setTheme('WHITE');
                }
            },
            {
                text : "Ночь",
                className : "menu-popup-no-icon"+ window.userSelectedTheme == 'BLACK' ? 'active' : '',
                onclick: function(e, item){
                    BX.PreventDefault(e);
                    setTheme('BLACK');
                }
            },

        ],
        {
			angle: null,

            events: {
                onPopupShow : function() {
 					// let leftPosition = document.querySelector(".main-site-theme-selector-7311").offsetLeft;
					// let topPosition = document.querySelector(".main-site-theme-selector-7311").offsetTop;
					// this.contentContainer.offsetTop = topPosition + "px";
					// this.contentContainer.offsetLeft = leftPosition + "px";
                },
                onPopupClose : function() {
                    BX.removeClass(this.bindElement, "selector-active");
                }
            }
        });
}

function setTheme(code){

    BX.ajax.runComponentAction('rs:theme.selector', 'setTheme', {
        mode: 'class',
        data: {
            themeCode: code,
        },
    }).then(function (response) {
        location.reload();
    }, function (response) {
        console.log(response);
    });
}