function findAds(input){
    BX.setCookie('ad_like', input.value, {expires: 86400});

    BX.ajax.runComponentAction(window.ads_editor.config.component, 'getComponentTemplateResult', {
        mode: 'class',
        data: {
            params: window.ads_editor.config.componentParams,
            adLike: input.value,
        },
    }).then(function (response) {
        let parser = new DOMParser(),
            doc = parser.parseFromString(response.data, 'text/html'),
            child = doc.querySelector('body').firstChild;

        document.querySelector('.'+Array.from(child.classList).join('.')).innerHTML = child.innerHTML;
        window.ads_editor.buildEditorBodies();
        document.querySelector('.'+Array.from(child.classList).join('.')).querySelector('input').focus();
    }, function (response) {
        showNotyMessage("Произошла ошибка");
    });
}