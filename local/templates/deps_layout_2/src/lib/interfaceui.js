class InterfaceUI {

    constructor( simpleGrid ) {
        this.layout = document.querySelector('html');
        this.hat = document.querySelector(".hat");
        this.footer = document.querySelector(".footer");
        this.portalGrid = document.querySelector('.portal-grid');
        this.workspaceEditButton = document.querySelector(".setup__button");
        this.simpleGrid = simpleGrid;
        this.oldSimpleGrid = simpleGrid;
        this.elementsNotToBeBlured = ['hat', 'portal-grid', 'footer', 'setup'];
        this.widgetItems = [
            'Погода',
            'Часы',
            'Библиотека',
            'Чат',
            'Заменить картридж'
        ];
        this.menuOutPosition = "90rem";
        this.menuInPosition = "-3000rem";
        this.lastAddedXPosition = 0;
        this.gazpromLavalamp = document.querySelector(".gazprom-lavalamp");
        this.lavalamps = document.querySelectorAll(".lavalamp");

        // инициализация методов
        this.interfaceEditButtonClickHandler();
        this.closeEditEnvironment();
        this.deleteWidget();
        this.removeConfirmOverflow();
        this.showSubmenuByAddWidgetButton();
        this.removeWidgets();
        this.saveState();
        this.updateLastAddedXPosition();
        this.writeSimpleDataIntoLocalStorage();
        document.addEventListener("click", (e) => {
            if(e.target.classList.contains('base-block')) {
                this.addGridItem(this.simpleGrid, e.target.innerText);
                e.target.parentNode.remove();
                if( document.querySelectorAll('.base-block').length<=0 ) {
                    document.querySelector(".hat__widget-heading").click();
                }
            }
        });
        this.lavalampHandler();
        this.lavalampRemoveHandler();
        this.setWidgetType();
    }

    // задание классов типов small, long, high
    setWidgetType() {
        let gridStackItems = this.portalGrid.querySelectorAll(".grid-stack-item ");
        gridStackItems.forEach((el) => {
            if( el.gridstackNode.h === 1 && el.gridstackNode.w === 1 ) {
                el.classList.add('grid-stack-item__small');
            } else if( el.gridstackNode.w === 2 && el.gridstackNode.w !== 1 ) {
                el.classList.add('grid-stack-item__long');
            } else if ( el.gridstackNode.h === 2 && el.gridstackNode.w === 1 ) {
                el.classList.add('grid-stack-item__high');
            }
        });
    }

    lavalampRemoveHandler() {
        this.gazpromLavalamp.addEventListener('mouseleave',() => {
            let activeLavalap = document.querySelector('.lavalamp_active');
            activeLavalap.style.opacity = "0";
        });
    };

    lavalampHandler() {
        this.lavalamps.forEach(lavalamp => {
            lavalamp.addEventListener('mouseenter', (event) => {

                let width = event.target.querySelector("span").offsetWidth;
                let left = event.target.querySelector("span").offsetLeft;

                let lavalampItem = document.querySelector(".lavalamp_active");
                lavalampItem.style.width = width + "px";
                lavalampItem.style.left = left + "px";
                lavalampItem.style.opacity = 1;
            })
        })
    }

    // запишем данные simple в localStorage
    writeSimpleDataIntoLocalStorage() {
        console.log('Запишем данные gridstack в localStorage');
    }

    // обновить при загрузке страницы lastAddedXPosition, колонка в которую добавлялся виджет
    updateLastAddedXPosition() {
        window.localStorage.setItem('lastAddedXPosition', 0);
    }

    // обработчик нажатия на кнопку редактирования интерфейса
    interfaceEditButtonClickHandler() {
        this.workspaceEditButton.addEventListener("click", (event) => {
            this.openCloseEditEnveronment();
            if( !(this.layout.classList.contains('html__editable')) ) {
                this.workspaceEditButton.innerText = "Нажмите, чтобы настроить домашнюю страницу";
            } else {
                this.workspaceEditButton.innerText = "Выйти из режима редактирования";
            }
        });
    }

    // открытие/закрытие режима редактирования
    openCloseEditEnveronment() {
        if( !(this.layout.classList.contains('html__editable')) ) {

            this.layout.classList.add('html__editable');
            this.hat.classList.add('hat_editable');
            this.footer.classList.add('footer_editable');
            this.portalGrid.classList.add('portal-grid_editable');

            this.scrollUp();
            this.simpleGrid.enable();

        } else {
            this.layout.classList.remove('html__editable');
            this.hat.classList.remove('hat_editable');
            this.footer.classList.remove('footer_editable')
            this.portalGrid.classList.remove('portal-grid_editable');

            this.simpleGrid.disable();
        }
        this.changeGridStackItems();
        this.createRemoveWidgetHandleButtons();
        this.blurUnblurElements();
    }

    // обработка нажатия на кнопку закрытия режима редактирования
    closeEditEnvironment() {
        document.addEventListener("click",(e) => {
           if( e.target.classList.contains("portal-grid_editable") ) {
               if(e.target.classList.contains("setup__button")) {
                   this.openCloseEditEnveronment();
               }
           }
        });
    }

    // заблюрить все элементы, кроме нужных
    blurUnblurElements() {
        let allFirstChildElements = (document.querySelector('body')).children;
        if( !(this.layout.classList.contains('html__editable')) ) {
            for( let i=0; i<allFirstChildElements.length; i++ ) {
                allFirstChildElements[i].style.filter = "none";
            }

        } else {
            for( let i=0; i<allFirstChildElements.length; i++ ) {
                let classListArray = allFirstChildElements[i].classList;
                if( !(this.elementsNotToBeBlured.includes(classListArray[0])) ) {
                    allFirstChildElements[i].style.filter = "blur(2.5rem)";
                }
            }
        }
    }

    // изменение grid-stack-items
    changeGridStackItems() {
        let gridStackItems = this.portalGrid.querySelectorAll(".grid-stack-item ");
        gridStackItems.forEach((gridStackItem) => {
            // добавление кнопки удаления текущему grid-stack-item
            this.addRemoveGridStackItemCloser(gridStackItem);
            // изменение стиля текущего grid-stack-item
            this.changeStylesOfGridStackItem(gridStackItem);
            // добавление дрожания текущего grid-stack-item
            this.addRemoveShakingForGridStackItem(gridStackItem);
        });
    }

    // добавление или удаление кнопки удаления для grid-stack-item
    addRemoveGridStackItemCloser(gridStackItem) {
        if( gridStackItem.querySelector(".grid-stack-item-closer") ) {
            gridStackItem.removeChild(gridStackItem.querySelector(".grid-stack-item-closer"));
        } else {
            let currentGridStackCloser = document.createElement("div");
            currentGridStackCloser.classList.add("grid-stack-item-closer");
            gridStackItem.appendChild(currentGridStackCloser);
        }
    }

    // изменить стили grid-stack-item при редактировании
    changeStylesOfGridStackItem( gridStackItem ) {
        if( !(gridStackItem.classList.contains("")) ) {
            gridStackItem.classList.add('grid-stack-item_edit-mode');
        } else {
            gridStackItem.classList.remove('grid-stack-item_edit-mode');
        }
    }

    // scroll вверх
    scrollUp() {
        $('html, body').animate({
            scrollTop: 0
        }, 500);
        setTimeout(
            this.openWidgetMenuWithTimeout,
            1000
        )
    }

    openWidgetMenuWithTimeout() {
        if( document.querySelector('body').scrollTop === 0 ) {
            let hatWidgetHeading = document.querySelector('.hat__widget-heading');
        }
    }

    // создание управляющих кнопок для виджетов
    createRemoveWidgetHandleButtons() {
        if( this.hat.classList.contains("hat_editable") ) {
            let hatAddWidgetButton = document.createElement("div");
                hatAddWidgetButton.classList.add(
                    "hat__widget-button",
                    "hat__add-widget",
                    "any__button"
                );
                hatAddWidgetButton.innerHTML = "<div class='hat__widget-heading'>+ Добавить виджет</div>";
                hatAddWidgetButton.appendChild(this.createSubmenuForWidgetButton());

            let hatSaveWorkspace = document.createElement('div');
                hatSaveWorkspace.classList.add(
                    "hat__widget-button",
                    "hat__save-workspace",
                    "any__button"
                );
            hatSaveWorkspace.innerText = "Сохранить";
            (this.hat.querySelector(".hat__edit")).appendChild(hatAddWidgetButton);
            (this.hat.querySelector(".hat__edit")).appendChild(hatSaveWorkspace);
        } else {
            (this.hat.querySelector(".hat__edit")).innerHTML = "";
        }
    }

    // создание выпадающего меню для кнопки Добавить виджет
    createSubmenuForWidgetButton() {
        let widgetSubMenu = document.createElement('div');
        widgetSubMenu.classList.add("hat__widget-submenu");

        let widgetListItemsHtml = "<ul>";
            this.widgetItems.forEach((litem)=>{
                let litemHmlt = this.createSubmenuSingleItem(litem);
                widgetListItemsHtml += litemHmlt;
            });
        widgetListItemsHtml += '</ul>';
        widgetListItemsHtml += "<div class='widget-search'><input placeholder='Найти виджет' type='text' /></div>";

        widgetSubMenu.innerHTML = widgetListItemsHtml;

        return widgetSubMenu;
    }

    // создание одного элемента
    createSubmenuSingleItem(litem) {
        let litemHtml = "";

            litemHtml += `<div class='base-block__wrapper'>
                            <li class='base-block'>${litem}</li>
                            <div class='base-block__description'><span>${litem}</span></div>
                          </div>`;

        return litemHtml;
    }

    showSubmenuByAddWidgetButton() {
        window.addEventListener('click', (e) => {
            if( e.target.classList.contains('hat__widget-heading') ) {
                if( document.querySelector('.hat__widget-submenu').style.top === this.menuOutPosition ) {
                    document.querySelector('.hat__widget-submenu').style.top = this.menuInPosition;
                    e.target.innerText = '+ Добавить виджет';
                } else {
                    e.target.innerText = 'Скрыть виджеты';
                    document.querySelector('.hat__widget-submenu').style.top = this.menuOutPosition;
                }
            }
        });
    }

    // создание дрожания для grid-stack-item
    addRemoveShakingForGridStackItem(gridStackItem) {
        if( gridStackItem.classList.contains("grid-stack-item_shaking") || gridStackItem.classList.contains("grid-stack-item_shaking-opposite") ) {

            gridStackItem.classList.remove("grid-stack-item_shaking");
            gridStackItem.classList.remove("grid-stack-item_shaking-opposite");
        } else {
            if( (gridStackItem.innerText.length)%2 === 0) {
                gridStackItem.classList.add("grid-stack-item_shaking");
            } else {
                gridStackItem.classList.add("grid-stack-item_shaking-opposite");
            }
        }

    }

    // удаление виджета из grid
    deleteWidget() {
        document.addEventListener("click", (event) => {
            if( event.target.classList.contains("grid-stack-item-closer") === true ) {

                let gridStackItemToBeDeletedId = event.target.parentNode.querySelector(".gridstack-main-class").getAttribute('id');

                let overflowBody = document.createElement("div");
                    overflowBody.classList.add("delete-widget__overflow");
                    overflowBody.innerHTML = `<div class="delete-widget__confirm">
                                                    <div class="confirm">
                                                        <div class="confirm__text">
                                                            Вы действительно хотите удалить виджет?
                                                        </div>
                                                        <div class="confirm__buttons">
                                                            <div class="confirm__yes" data-customid=${gridStackItemToBeDeletedId}>
                                                                Да
                                                            </div>
                                                            <div class="confirm__no">
                                                                Нет
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>`;
                document.body.appendChild(overflowBody);
            }
        });
    }

    // удаление confirm overflow
    removeConfirmOverflow() {
        window.addEventListener("click", function(event) {
            if( event.target.classList.contains("confirm__no") === true || event.target.classList.contains("confirm__yes") === true) {
                if( document.querySelector(".delete-widget__overflow") !== 'undefined' ) {
                    if( document.querySelector(".delete-widget__overflow") !== null ) {
                        document.querySelector(".delete-widget__overflow").remove();
                    }
                }
            }
        });
    }

    // добавление нового виджета
    addGridItem(simpleGrid, info) {

        // установим колонку 0, 1 или 2, куда будет добавлять новый виджет
        this.setLastAddedXPosition();

        let gridStackItem =  this.simpleGrid.addWidget({
            x: this.lastAddedXPosition,
            y: 0,
            w: 1,
            h: 1,
            content: `<div class='gridstack-main-class' id='${ (Math.random() + 1).toString(36).substring(7) }' ><div></div><div>${info}</div></div>`
        });



        // добавление кнопки удаления текущему grid-stack-item
        this.addRemoveGridStackItemCloser(gridStackItem);
        // изменение стиля текущего grid-stack-item
        this.changeStylesOfGridStackItem(gridStackItem);
        // добавление дрожания текущего grid-stack-item
        this.addRemoveShakingForGridStackItem(gridStackItem);

    }

    // установка x позиции в localStorage
    setLastAddedXPosition() {
        if( window.localStorage.getItem('lastAddedXPosition') !== null ) {
            let position = parseInt(window.localStorage.getItem('lastAddedXPosition'));
            this.lastAddedXPosition = position;
            position > 1 ? position = 0 : position ++;
            window.localStorage.setItem(
                'lastAddedXPosition',
                position
            );
        }
    }

    // удаление виджета
    removeWidgets() {
        document.addEventListener('click', (e) => {
            if(e.target.classList.contains('confirm__yes')) {
                let cellToBeDeleted = document.getElementById(e.target.dataset.customid);
                let gridStackItemToBeDeleted = cellToBeDeleted.parentNode.parentNode;
                this.removeDefinedWidget(gridStackItemToBeDeleted);
            }
        })
    }

    removeDefinedWidget(gridStackItemToBeDeleted) {
        this.simpleGrid.removeWidget(gridStackItemToBeDeleted);
        this.removeConfirmOverflow();
    }

    // сохранить состояние грида
    saveState() {
        document.addEventListener('click', (event) => {
            if(event.target.classList.contains('hat__save-workspace')) {
                this.openCloseEditEnveronment();
            }
        })
    }

}