$(function() {

    // Сервисы, функционал отображения превью текста при клике
    // + Добавление в избранное
    $(".service-text").on(
        'click',
        function (e) {
            var preview = $(this).closest(".servise-item").children(".service-preview").html();
            //console.log(preview);
            $(".favorites-text").html(preview);

            var flag = '';

            $(".add_fav").bind(
                'click',
                function (e) {
                    //console.log('add');
                    $(this).hide();
                    $(this).siblings('.del_fav').fadeIn();

                    flag = 'add';
                    params = {
                        elid: $(this).data('elid'),
                        name: $(this).data('name'),
                        url: $(this).data('url'),
                        uid: $(this).data('uid')
                    };

                    // добавить в избранное
                    BX.ajax.runComponentAction('bitrix:industry.services',
                        'addDelFavorite', {
                            mode: 'class',
                            async: true,
                            data: {flag: flag, params: params},
                        })
                        .then(function (response) {
                            if (response.status === 'success') {
                                //console.log(response);
                                window.location.reload();
                                //getFavorites();

                            }
                        })
                        .catch(function (response) {
                            //console.log(response);
                        });

                }
                );

            $(".del_fav").bind(
                'click',
                function (e) {
                    //console.log('del');
                    $(this).hide();
                    $(this).siblings('.add_fav').fadeIn();

                    flag = 'del';
                    params = {
                        elid: $(this).data('elid'),
                        name: $(this).data('name'),
                        url: $(this).data('url'),
                        uid: $(this).data('uid')
                    };

                    // убрать из избранного
                    BX.ajax.runComponentAction('bitrix:industry.services',
                        'addDelFavorite', {
                            mode: 'class',
                            async: true,
                            data: {flag: flag, params: params},
                        })
                        .then(function (response) {
                            if (response.status === 'success') {
                                //console.log(response);
                                window.location.reload();
                                //getFavorites();

                            }
                        })
                        .catch(function (response) {
                            //console.log(response);
                        });

                }
            );

        }
    );

    function getFavorites() {

        BX.ajax.runComponentAction('bitrix:industry.services',
            'getFavorites', {
                mode: 'class',
                async: true
            })
            .then(function (response) {
                if (response.status === 'success') {
                    console.log(response.data);

                    var str = '';
                    var url = '';
                    var name = '';

                    for(var i = 0; i <= 5; i++){
                        url = '#!';
                        name = '';

                        if(typeof response.data[i] !== "undefined") {
                            name = response.data[i].NAME;
                        }

                        if(typeof response.data[i] !== "undefined") {
                            url = response.data[i].URL;
                        }
                        str += ' <a class="favorites-item" href="' + url + '"><p>' + name + '</p></a>';
                    }

                    $('.favorites-items').html(str);

                    //console.log(str);

                }
            })
            .catch(function (response) {
                //console.log(response);
            });
    }

    // Быстрый поиск
    function submit(evt) {
        evt.preventDefault();
    }
    function filter(evt) {
        evt.preventDefault();
        var input = document.querySelector('#search-input');
        var inputValue = input.value.toUpperCase();
        var cards = document.querySelectorAll('.servise-item');
        cards.forEach(
            function getMatch(info) {
                heading = info.querySelector('.src-text');
                headingContent = heading.innerHTML.toUpperCase();

                if (headingContent.includes(inputValue)) {
                    info.classList.add('show');
                    info.classList.remove('hide');
                }
                else {
                    info.classList.add('hide');
                    info.classList.remove('show');
                }
            }
        )
    }
    function autoReset() {
        var input = document.querySelector('#search-input');
        var cards = document.querySelectorAll('.servise-item');
        cards.forEach(
            function getMatch(info) {
                if (input.value == null, input.value == "") {
                    info.classList.remove('show');
                    info.classList.remove('show');
                }
                else {
                    return;
                }
            }
        )
    }
    var form = document.querySelector('.search-form');
    form.addEventListener('keyup', filter);
    form.addEventListener('keyup', autoReset);
    form.addEventListener('submit', submit);

});

BX.ready(function(){
    let owlSerss = $('.servises-items');
    owlSerss.owlCarousel({
        loop:false,
        margin:14,
        items:6,
        autoWidth: true,
        nav:false,
        dots : false,
        responsive:{

        }
    });
})