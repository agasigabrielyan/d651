window.onload = function() {

    let collaboration = `
        <div class="widget">
            <div class="widget__header">
                <div class="widget__name">
                    Управление 1
                </div>
                <a href="" class="widget__link"></a>
            </div>
            <div class="widget__body">
                <div class="ghost-text">
                ФИО Руководителя<br>
                и направление деятельности
                </div>
            </div>
        </div>
    `;
    let myEvents = `
       <div class="widget">
            <div class="widget__header">
                <div class="widget__name">
                    Управление 2
                </div>
                <a href="" class="widget__link"></a>
            </div>
            <div class="widget__body">
                <div class="ghost-text">
                ФИО Руководителя<br>
                и направление деятельности
                </div>
            </div>
        </div>
    `;
    let myServices = `
        <div class="widget">
            <div class="widget__header">
                <div class="widget__name">
                    Управление 3
                </div>
                <a href="" class="widget__link"></a>
            </div>
            <div class="widget__body">
                <div class="ghost-text">
                ФИО Руководителя<br>
                и направление деятельности
                </div>
            </div>
        </div>
    `;
    let sadPaoGazprom = `
        <div class="widget">
            <div class="widget__header">
                <div class="widget__name">
                    Управление 4
                </div>
                <a href="" class="widget__link"></a>
            </div>
            <div class="widget__body">
                <div class="ghost-text">
                ФИО Руководителя<br>
                и направление деятельности
                </div>
            </div>
        </div>
    `;
    let statusASES = `
        <div class="widget">
            <div class="widget__header">
                <div class="widget__name">
                    Новости
                </div>
                <a class="widget__link"></a>
            </div>
            <div class="widget__body">
                <div class="my-news">
                    <div class="my-news__search">
                        <input type="text" placeholder="поиск" />
                    </div>
                    <div class="my-news__filter">
                        <div class="my-news__filter-item">Все новости</div>
                        <div class="my-news__filter-item">Группы</div>
                        <div class="my-news__filter-item">Отдых</div>
                        <div class="my-news__filter-item">Департамент</div>
                        <div class="my-news__filter-item">Мероприятия</div>
                        <div class="my-news__filter-item">Сообщества</div>
                    </div>
                    <div class="my-news__list">
                        <ul>
                            <li>
                                <div class="my-news__image"><img src="/local/templates/deps_layout_2/src/images/no-photo.svg" /></div>
                                <div class="my-news__info">
                                    <div class="my-news__top">
                                        <span class='my-news__date'>25 Января</span>
                                           <div>
                                                <span class='my-news__watch'>
                                                    <img src='/local/templates/deps_layout_2/src/images/watch.svg' />
                                                    630
                                                </span>
                                                <span class='my-news__like'>
                                                    <img src='/local/templates/deps_layout_2/src/images/like.svg' />
                                                    63
                                                </span>
                                            </div>                                    
                                    </div>
                                    <div class="my-news__name">
                                        Непутевые работники и как с ними справляться?
                                    </div>
                                    <div class="my-news__tags">
                                        #Мероприятия
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="my-news__image"><img src="/local/templates/deps_layout_2/src/images/no-photo.svg" /></div>
                                <div class="my-news__info">
                                    <div class="my-news__top">
                                        <span class='my-news__date'>25 Января</span>
                                           <div>
                                                <span class='my-news__watch'>
                                                    <img src='/local/templates/deps_layout_2/src/images/watch.svg' />
                                                    630
                                                </span>
                                                <span class='my-news__like'>
                                                    <img src='/local/templates/deps_layout_2/src/images/like.svg' />
                                                    63
                                                </span>
                                            </div>    
                                    </div>
                                    <div class="my-news__name">
                                        Непутевые работники и как с ними справляться?
                                    </div>
                                    <div class="my-news__tags">
                                        #Мероприятия
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="my-news__image"><img src="/local/templates/deps_layout_2/src/images/no-photo.svg" /></div>
                                <div class="my-news__info">
                                    <div class="my-news__top">
                                        <span class='my-news__date'>25 Января</span>

                                            <div>
                                                <span class='my-news__watch'>
                                                    <img src='/local/templates/deps_layout_2/src/images/watch.svg' />
                                                    630
                                                </span>
                                                <span class='my-news__like'>
                                                    <img src='/local/templates/deps_layout_2/src/images/like.svg' />
                                                    63
                                                </span>
                                            </div>    

                                    </div>
                                    <div class="my-news__name">
                                        Непутевые работники и как с ними справляться?
                                    </div>
                                    <div class="my-news__tags">
                                        #Мероприятия
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `;
    let purASBU = `
        <div class="widget">
            <div class="widget__header">
                <div class="widget__name">
                    Доска объявлений
                </div>
                <a class="widget__link"></a>
            </div>
            <div class="widget__body">
                    <div class="my-news">
                    <div class="my-news__search">
                        <input type="text" placeholder="поиск" />
                    </div>
                    <div class="my-news__list">
                        <ul>
                            <li>
                                <div class="my-news__info">
                                    <div class="my-news__top">
                                        <span class='my-news__date'>21 Июля</span>                                  
                                    </div>
                                    <div class="my-news__name">
                                        Собираем команду для занятий волейболом
                                    </div>
                                    <div class="my-news__tags">
                                        #Спорт
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    `;
    let myNews = `
        <div class="widget">
            <div class="widget__header">
                <div class="widget__name">
                    Календарь
                </div>
                <a class="widget__link"></a>
            </div>
            <div class="widget__body">
                <div class="my-calendar">                    
                    <div class="my-calendar__search">

                            <div class="my-news__search">
                                <input type="text" placeholder="поиск">
                            </div>


                            <div class="my-calendar__icons">
                                <div data-type='calendar' class="my-calendar__icon my-calendar__icon_active">
                                    <img src="/local/templates/deps_layout_2/src/images/calendar.svg" />
                                </div>
                                <div data-type='event' class="my-calendar__icon">
                                    <img src="/local/templates/deps_layout_2/src/images/listicon.svg" />
                                </div>
                            </div>

                    </div>
                    
                    <div class="my-calendar__area-wrapper">
                         <div class="my-calendar__area my-calendar__area_calendar my-calendar__area_active">
                            <div class="my-calendar__calendar">
                                <div class="my-calendar__title">
                                    <button><img src='/local/templates/deps_layout_2/src/images/calendar__left-arrow.svg' /></button>
                                    <span>Июль 2023</span>
                                    <button><img src='/local/templates/deps_layout_2/src/images/calendar__right-arrow.svg' /></button>
                                </div>
                                <div class="my-calendar__digits">
                                    <table>
                                        <thead>
                                            <tr>
                                                <td><span> Пн   </span></td>
                                                <td><span> Вт   </span></td>
                                                <td><span> Ср   </span></td>
                                                <td><span> Чт   </span></td>
                                                <td><span> Пт   </span></td>
                                                <td><span> Сб   </span></td>
                                                <td><span> Вс   </span></td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span> 1    </span></td>
                                                <td><span> 2    </span></td>
                                                <td><span> 3    </span></td>
                                                <td><span> 4    </span></td>
                                                <td><span> 5    </span></td>
                                                <td><span> 6    </span></td>
                                                <td><span> 7    </span></td>
                                            </tr>
                                            <tr>
                                                <td><span> 8    </span></td>
                                                <td><span> 9    </span></td>
                                                <td class="my-calendar__event_blue"><span> 10   </span></td>
                                                <td class="my-calendar__event_outline"><span> 11    </span></td>
                                                <td class="my-calendar__event_vacation"><span> 12   </span></td>
                                                <td><span> 13   </span></td>
                                                <td><span> 14   </span></td>
                                            </tr>
                                            <tr>
                                                <td><span> 15   </span></td>
                                                <td><span> 16   </span></td>
                                                <td><span> 17   </span></td>
                                                <td><span> 18   </span></td>
                                                <td><span> 19   </span></td>
                                                <td><span> 20   </span></td>
                                                <td><span> 21   </span></td>
                                            </tr>
                                            <tr>
                                                <td><span> 22   </span></td>
                                                <td><span> 23   </span></td>
                                                <td><span> 24   </span></td>
                                                <td><span> 25   </span></td>
                                                <td><span> 26   </span></td>
                                                <td><span> 27   </span></td>
                                                <td><span> 28   </span></td>
                                            </tr>
                                            <tr>
                                                <td><span> 29   </span></td>
                                                <td><span> 30   </span></td>
                                                <td><span> 31   </span></td>
                                                <td><span>  </span></td>
                                                <td><span>  </span></td>
                                                <td><span>  </span></td>
                                                <td><span>  </span></td>
                                            </tr>
                                        </tbody>                                        
                                    </table>
                                </div>
                            </div>                        
                        </div>
                        <div class="my-calendar__area my-calendar__area_events">
                            <div class="my-events">
                                <div class="my-events__date">
                                    22 марта 2023
                                </div>
                                <div class="my-events__list">
                                    <div class="my-events__event">
                                        <table>
                                            <tr>
                                                <td>10:00</td>
                                                <td>
                                                    <span class='event-title'>Совещания на Новоданиловской</span><br/><span>#Мероприятия</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>10:00</td>
                                                <td>
                                                    <span class='event-title'>Совещания на Новоданиловской</span><br/><span>#Мероприятия</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>10:00</td>
                                                <td>
                                                    <span class='event-title'>Совещания на Новоданиловской</span><br/><span>#Мероприятия</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="my-events__date">
                                    22 марта 2023
                                </div>
                                <div class="my-events__list">
                                    <div class="my-events__event">
                                        <table>
                                            <tr>
                                                <td>10:00</td>
                                                <td>
                                                    <span class='event-title'>Совещания на Новоданиловской</span><br/><span>#Мероприятия</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="my-events__date">
                                    22 марта 2023
                                </div>
                                <div class="my-events__list">
                                    <div class="my-events__event">
                                        <table>
                                            <tr>
                                                <td>10:00</td>
                                                <td>
                                                    <span class='event-title'>Совещания на Новоданиловской</span><br/><span>#Мероприятия</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="my-calendar__add">
                            <div class="my-calendar__button">
                                + <span>Создать событие</span>
                            </div>
                    </div>                    
                </div>
            </div>
        </div>
    `;

    let simple = [
		{x: 0, y: 0, w: 2, h: 2, content: `<div class='gridstack-main-class' id='${ (Math.random() + 1).toString(36).substring(7) }' >${statusASES}</div>`},
        {x: 2, y: 0, w: 2, h: 2, content: `<div class='gridstack-main-class' id='${ (Math.random() + 1).toString(36).substring(7) }' >${purASBU}</div>`},
        {x: 4, y: 0, w: 2, h: 2, content: `<div class='gridstack-main-class' id='${ (Math.random() + 1).toString(36).substring(7) }' >${myNews}</div>`},
        {x: 0, y: 1, w: 3, h: 1, content: `<div class='gridstack-main-class' id='${ (Math.random() + 1).toString(36).substring(7) }' >${collaboration}</div>`},
        {x: 4, y: 1, w: 3, h: 1, content: `<div class='gridstack-main-class' id='${ (Math.random() + 1).toString(36).substring(7) }' >${myEvents}</div>`},
        {x: 0, y: 2, w: 3, h: 1, content: `<div class='gridstack-main-class' id='${ (Math.random() + 1).toString(36).substring(7) }' >${myServices}</div>`},
        {x: 4, y: 2, w: 3, h: 1, content: `<div class='gridstack-main-class' id='${ (Math.random() + 1).toString(36).substring(7) }' >${sadPaoGazprom}</div>`},
    ];

    let simpleGrid = GridStack.init({
        column: 6,
        cellHeight: "280rem",
        disableOneColumnMode: true,
        disableDrag: true,
        disableResize: true,
        alwaysShowResizeHandle: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
        margin: "10rem",
    }, '#simple-grid');

    simpleGrid.load(simple);

    // при попытке переноса блока отрабатывает этот метод
    simpleGrid.on('dragstart', function(e, ui) {
        var grid = this;
        var element = e.target;
        element.style.cursor = "pointer";
        element.classList.add("grid-stack-item_under-edit");
        element.classList.remove("grid-stack-item_shaking");
        element.classList.remove("grid-stack-item_shaking-opposite");
    });

    // при остановке переноса отрабатывает этот метод
    simpleGrid.on('dragstop', function(e, ui) {
        var grid = this;
        var element = e.target;
        element.style.cursor = "auto";
        element.classList.remove("grid-stack-item_under-edit");
        if( document.querySelector("html").classList.contains("html__editable") ) {
            element.classList.add("grid-stack-item_shaking");
        }
    });


    simpleGrid.on('resizestart', function(event, el) {
        // зададим класс grid-item__under-resizing при попытке ресайзить grid-item
        el.classList.add("grid-stack-item__under-resizing");
    });

    // при изменинии размеров отрабатывает этот метод
    simpleGrid.on('resize', function(event, el) {

        if(el.gridstackNode.w > 2) {
            el.gridstackNode.w = 2;
        }

        if( el.gridstackNode.h > 2 ) {
            el.gridstackNode.h = 1;
        }

        if( el.gridstackNode.w >= 2 && el.gridstackNode.h === 2) {
            el.gridstackNode.h = 1;
        }

        // добавим и удалим grid-stack-item чтобы инициировать изменение сетки
        simpleGrid.addWidget('<div id="just-a-widget" class="grid-stack-item"><div class="grid-stack-item-content">hello</div></div>', {w: 1, h: 1});
        simpleGrid.removeWidget(document.getElementById("just-a-widget"));
    });

    simpleGrid.on('resizestop', function(event, el) {
        el.classList.remove("grid-stack-item__under-resizing");

        // удалим все классы типа блока(small, long, high)
        el.classList.remove('grid-stack-item__small','grid-stack-item__long','grid-stack-item__high');

        if( el.gridstackNode.h === 1 && el.gridstackNode.w === 1 ) {
            el.classList.add('grid-stack-item__small');
        } else if( el.gridstackNode.w === 2 && el.gridstackNode.w !== 1 ) {
            el.classList.add('grid-stack-item__long');
        } else if ( el.gridstackNode.h === 2 && el.gridstackNode.w === 1 ) {
            el.classList.add('grid-stack-item__high');
        }
    });

    // инициализация интерфейса
    let interfaceUiObj = new InterfaceUI(simpleGrid);
}
