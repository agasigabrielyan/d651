BX.ready(function() {

    let selectedDate = new Date(document.querySelector('#sheduler-year').value, document.querySelector('#sheduler-month').value-1, document.querySelector('#sheduler-day').value);
    window.calendarRS = new RSCalendar(document.querySelector('.calendar-instanse-popUp'), selectedDate, calendarView);

    scheduler.config.first_hour = 0;
    scheduler.config.last_hour = 24;
    scheduler.config.event_duration = '1439';
    scheduler.config.details_on_dblclick = false;
    scheduler.config.resize_month_events = false;
    scheduler.config.drag_create = false;
    scheduler.config.drag_resize=false;
    scheduler.config.container_autoresize = false;
    scheduler.config.cascade_event_display = false;
    scheduler.config.drag_event_body = false;
    scheduler.config.full_day = true;
    scheduler.config.min_grid_size = 100;
    scheduler.config.day_date = "%D";
    scheduler.plugins({
        tooltip: true
    });
    scheduler.config.hour_size_px = 60;
    scheduler.config.timeout_to_display = 50;
    scheduler.config.timeout_to_hide = 50;

    scheduler.templates.hour_scale = function(date){
        return window.calendarRS.onHourBuild(date);
    }

    scheduler.i18n.setLocale('ru');

    scheduler.i18n.setLocale({
        date:{
            month_full:["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь",
                "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
            day_full:["Вск", "Пн", "Вт", "Ср", "Чт",
                "Пт", "Сб"],
            day_short:["Вск", "Пн", "Вт", "Ср", "Чт",
                "Пт", "Сб"],
        },
    });

    scheduler.init('scheduler_here', selectedDate, calendarView);

    scheduler.parse(eventsList);

    window.calendarRS.onViewChange(calendarView, selectedDate);

    scheduler.attachEvent("onBeforeLightbox", function (id) {
        return window.calendarRS.onBeforeLightbox(id);
    });

    scheduler.attachEvent("onEventCreated", function (id) {
        return window.calendarRS.onEventCreated(id);
    });

    scheduler.attachEvent("onEventChanged", function(id){
        return window.calendarRS.onEventChanged(id);
    });

    scheduler.attachEvent("onViewChange", function(mode,date){
        return window.calendarRS.onViewChange(mode,date);
    });
    scheduler.attachEvent("onClick", function(id){
        return window.calendarRS.onEventClick(id);
    });

    scheduler.templates.tooltip_text = function(start,end,event) {
        return window.calendarRS.onCreateToolTip(start,end,event)
    };

});

function correctTime(el){
    if(!el.value)
        return;

    let date = el.value.split('.');

    date = new Date(date[2], date[1]-1, date[0]);

    if(date.getHours() == 0 && date.getMinutes() == 0){
        date.setTime(date.getTime() - 60);
        //el.value = date.toLocaleString().replaceAll(',', '');
    }
}

function checkTime(date){
    if(!date)
        return date;

    if(date.getHours() == 0 && date.getMinutes() == 0)
        date.setTime(date.getTime() - 60);

    return date;

}