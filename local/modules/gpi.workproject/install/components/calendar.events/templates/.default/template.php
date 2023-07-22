<script>
    const calendarParams = <?=\Bitrix\Main\Web\Json::encode($arParams)?>;
</script>

<div class="row">
    <div class="<?=$arResult['list'] == 'Y'? 'col-7' : 'col-0'?> calendar-list-container">

        <?if(array_intersect(['W', 'X'], $arParams['USER_PERMISSIONS']) ):?>

            <div onclick="window.calendarRS.showPopUp({TITLE:''});" class="ui-btn-sm feed-btn">Создать событие</div>

        <?endif;?>

        <div class="title">Мероприятия &nbsp; <span class="calendar-selected-date"></span></div>
        <div class="calendar-list">
            <div class="left-data"></div>
            <div class="center-data"></div>
            <div class="right-data"></div>
        </div>
    </div>

    <div class="<?=$arResult['list'] == 'Y'? 'col-5' : 'col-12'?> shaduler-here-container">
        <div class="flex FlexLeftMargin dateFilter">

            <label class="checkbox-rs">
                <input onchange="window.calendarRS.changeView(this);" <?if($arResult['calendarView'] == 'week'):?>checked<?endif;?> type="checkbox" class="month-7310">
                <span class="checkbox-rs-switch"></span>
            </label>

            <div class="ui-ctl ui-ctl-textbox">
                <input onchange="window.calendarRS.setShedulerDate()" class="ui-ctl-element" id="sheduler-day" value="<?=$arResult['day']?>" type="number" min="1" max="31">
            </div>

            <div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
                <select onchange="window.calendarRS.setShedulerDate()" class="ui-ctl-element" id="sheduler-month">
                    <option <?if($arResult['month'] == 1):?> selected <?endif;?> value="01">Январь</option>
                    <option <?if($arResult['month'] == 2):?> selected <?endif;?> value="02">Февраль</option>
                    <option <?if($arResult['month'] == 3):?> selected <?endif;?> value="03">Март</option>
                    <option <?if($arResult['month'] == 4):?> selected <?endif;?> value="04">Апрель</option>
                    <option <?if($arResult['month'] == 5):?> selected <?endif;?> value="05">Май</option>
                    <option <?if($arResult['month'] == 6):?> selected <?endif;?> value="06">Июнь</option>
                    <option <?if($arResult['month'] == 7):?> selected <?endif;?> value="07">Июль</option>
                    <option <?if($arResult['month'] == 8):?> selected <?endif;?> value="08">Август</option>
                    <option <?if($arResult['month'] == 9):?> selected <?endif;?> value="09">Сентябрь</option>
                    <option <?if($arResult['month'] == 10):?> selected <?endif;?> value="10">Октябрь</option>
                    <option <?if($arResult['month'] == 11):?> selected <?endif;?> value="11">Ноябрь</option>
                    <option <?if($arResult['month'] == 12):?> selected <?endif;?> value="12">Декабрь</option>
                </select>

            </div>

            <div class="ui-ctl ui-ctl-textbox">
                <input onchange="window.calendarRS.setShedulerDate()" class="ui-ctl-element" id="sheduler-year" value="<?=$arResult['year']?>" type="number" min="2000" max="<?=$arResult['year']+5?>">
            </div>

            <?if(array_intersect(['X'], $arParams['USER_PERMISSIONS']) ):?>

                <div class='ui-btn ui-btn-icon-setting' onclick="openSidePanel('<?=$arParams['SEF_FOLDER']?>settings/', 600)"></div>

            <?endif;?>
        </div>


        <div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100vh;'>
            <div class="dhx_cal_navline">
                <div class="dhx_cal_prev_button">&nbsp;</div>
                <div class="dhx_cal_date"></div>
                <div class="dhx_cal_next_button">&nbsp;</div>

                <svg class="minimize-size" onclick="window.calendarRS.showShadulerDayList();" xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="0 0 32 32" version="1.1">
                    <path d="M11.975 10.838l-0.021-7.219c-0.009-0.404-0.344-0.644-0.748-0.654l-0.513-0.001c-0.405-0.009-0.725 0.343-0.716 0.747l0.028 4.851-8.321-8.242c-0.391-0.391-1.024-0.391-1.414 0s-0.391 1.024 0 1.414l8.285 8.207-4.721 0.012c-0.404-0.009-0.779 0.27-0.84 0.746l0.001 0.513c0.010 0.405 0.344 0.739 0.748 0.748l7.172-0.031c0.008 0.001 0.013 0.003 0.020 0.003l0.366 0.008c0.201 0.005 0.383-0.074 0.512-0.205 0.132-0.13 0.178-0.311 0.175-0.514l-0.040-0.366c0.001-0.007 0.027-0.012 0.027-0.019zM20.187 11.736c0.129 0.13 0.311 0.21 0.512 0.205l0.366-0.008c0.007 0 0.012-0.002 0.020-0.004l7.172 0.031c0.404-0.009 0.738-0.344 0.747-0.748l0.001-0.513c-0.061-0.476-0.436-0.755-0.84-0.746l-4.721-0.012 8.285-8.207c0.391-0.391 0.391-1.024 0-1.414s-1.023-0.391-1.414 0l-8.32 8.241 0.027-4.851c0.009-0.404-0.311-0.756-0.715-0.747l-0.513 0.001c-0.405 0.010-0.739 0.25-0.748 0.654l-0.021 7.219c0 0.007 0.027 0.012 0.027 0.020l-0.040 0.366c-0.005 0.203 0.043 0.384 0.174 0.514zM11.813 20.232c-0.13-0.131-0.311-0.21-0.512-0.205l-0.366 0.009c-0.007 0-0.012 0.003-0.020 0.003l-7.173-0.032c-0.404 0.009-0.738 0.343-0.748 0.747l-0.001 0.514c0.062 0.476 0.436 0.755 0.84 0.745l4.727 0.012-8.29 8.238c-0.391 0.39-0.391 1.023 0 1.414s1.024 0.39 1.414 0l8.321-8.268-0.028 4.878c-0.009 0.404 0.312 0.756 0.716 0.747l0.513-0.001c0.405-0.010 0.739-0.25 0.748-0.654l0.021-7.219c0-0.007-0.027-0.011-0.027-0.019l0.040-0.397c0.005-0.203-0.043-0.384-0.174-0.514zM23.439 22.028l4.727-0.012c0.404 0.009 0.779-0.27 0.84-0.745l-0.001-0.514c-0.010-0.404-0.344-0.739-0.748-0.748h-7.172c-0.008-0-0.013-0.003-0.020-0.003l-0.428-0.009c-0.201-0.006-0.384 0.136-0.512 0.267-0.131 0.13-0.178 0.311-0.174 0.514l0.040 0.366c0 0.008-0.027 0.012-0.027 0.019l0.021 7.219c0.009 0.404 0.343 0.644 0.748 0.654l0.544 0.001c0.404 0.009 0.725-0.343 0.715-0.747l-0.027-4.829 8.352 8.22c0.39 0.391 1.023 0.391 1.414 0s0.391-1.023 0-1.414z"/>
                </svg>

                <svg class="maximize-size" onclick="window.calendarRS.hideShadulerDayList();" xmlns="http://www.w3.org/2000/svg" fill="#000000" preserveAspectRatio="xMidYMid" width="31.812" height="31.906" viewBox="0 0 31.812 31.906">
                    <path d="M31.728,31.291 C31.628,31.535 31.434,31.729 31.190,31.830 C31.069,31.881 30.940,31.907 30.811,31.907 L23.851,31.907 C23.301,31.907 22.856,31.461 22.856,30.910 C22.856,30.359 23.301,29.913 23.851,29.913 L28.405,29.908 L19.171,20.646 C18.782,20.257 18.782,19.626 19.171,19.236 C19.559,18.847 20.188,18.847 20.577,19.236 L29.906,28.593 L29.906,23.906 C29.906,23.355 30.261,22.933 30.811,22.933 C31.360,22.933 31.805,23.379 31.805,23.930 L31.805,30.910 C31.805,31.040 31.779,31.169 31.728,31.291 ZM30.811,8.973 C30.261,8.973 29.906,8.457 29.906,7.906 L29.906,3.313 L20.577,12.669 C20.382,12.864 20.128,12.962 19.874,12.962 C19.619,12.962 19.365,12.864 19.171,12.669 C18.782,12.280 18.782,11.649 19.171,11.259 L28.497,1.906 L23.906,1.906 C23.356,1.906 22.856,1.546 22.856,0.996 C22.856,0.445 23.301,-0.001 23.851,-0.001 L30.811,-0.001 C30.811,-0.001 30.811,-0.001 30.812,-0.001 C30.941,-0.001 31.069,0.025 31.190,0.076 C31.434,0.177 31.628,0.371 31.728,0.615 C31.779,0.737 31.805,0.866 31.805,0.996 L31.805,7.976 C31.805,8.526 31.360,8.973 30.811,8.973 ZM3.387,29.908 L7.942,29.913 C8.492,29.913 8.936,30.359 8.936,30.910 C8.936,31.461 8.492,31.907 7.942,31.907 L0.982,31.907 C0.853,31.907 0.724,31.881 0.602,31.830 C0.359,31.729 0.165,31.535 0.064,31.291 C0.014,31.169 -0.012,31.040 -0.012,30.910 L-0.012,23.930 C-0.012,23.379 0.433,22.933 0.982,22.933 C1.532,22.933 1.906,23.355 1.906,23.906 L1.906,28.573 L11.216,19.236 C11.605,18.847 12.234,18.847 12.622,19.236 C13.011,19.626 13.011,20.257 12.622,20.646 L3.387,29.908 ZM11.919,12.962 C11.665,12.962 11.410,12.864 11.216,12.669 L1.906,3.332 L1.906,7.906 C1.906,8.457 1.532,8.973 0.982,8.973 C0.433,8.973 -0.012,8.526 -0.012,7.976 L-0.012,0.996 C-0.012,0.866 0.014,0.737 0.064,0.615 C0.165,0.371 0.359,0.177 0.602,0.076 C0.723,0.025 0.852,-0.001 0.980,-0.001 C0.981,-0.001 0.981,-0.001 0.982,-0.001 L7.942,-0.001 C8.492,-0.001 8.936,0.445 8.936,0.996 C8.936,1.546 8.456,1.906 7.906,1.906 L3.296,1.906 L12.622,11.259 C13.011,11.649 13.011,12.280 12.622,12.669 C12.428,12.864 12.174,12.962 11.919,12.962 Z"/>
                </svg>
            </div>
            <div class="dhx_cal_header"></div>
            <div class="dhx_cal_data"></div>
        </div>
    </div>
</div>


<form class="calendar-instanse-popUp" hidden>
    <div class="row">
        <input class="field" hidden name="ID">
        <input class="field" hidden name="TITLE" required>
        <input class="field" value="<?=$arParams['CALENDAR_ID']?>" hidden name="CALENDAR_ID">

        <?if($arParams['CALENDAR_ID'] != 1):?>

            <div class="private-swicher">
                <input hidden type="number" name="IS_PUBLICK" class="field">
                <label class="checkbox-rs">
                    <input onchange="this.closest('.legend').querySelector('[name=IS_PUBLICK]').value = this.checked? 1 : 0;" type="checkbox">
                    <span class="checkbox-rs-switch"></span>
                </label>
            </div>
        <?endif;?>


        <select onchange="window.calendarRS.showEventTypeProps()" class="field form-type-selector" name="TYPE">
            <option selected value="EVENT">Событие</option>
            <option value="TASK">Задача</option>
            <option value="WORK">Работа</option>
        </select>

        <div class="col-6">
            <div class="legend">
                <label for="STARTED"  onclick="BX.calendar({node:  this.parentNode.querySelector('input') , field:  this.parentNode.querySelector('input') , bTime:  true , bSetFocus:  true});">Дата начала</label>
                <input class="field" name="STARTED" type="text"  onclick="BX.calendar({node:  this , field:  this , bTime:  true , bSetFocus:  true});" required>
            </div>
        </div>

        <div class="col-6">
            <div class="legend">
                <label for="ENDED"  onclick="BX.calendar({node:  this.parentNode.querySelector('input') , field:  this.parentNode.querySelector('input') , bTime:  true , bSetFocus:  true, callback_after: correctTime(this)});">Дата окончания</label>
                <input class="field" name="ENDED" type="text"  onclick="BX.calendar({node:  this , field:  this , bTime:  true , bSetFocus:  true, callback_after: correctTime(this)});" required>
            </div>
        </div>

        <div data-event-toggle-prop data-target="TASK,WORK" class="col-6 hidden">
            <div class="legend">
                <label for="FACT_STARTED"  onclick="BX.calendar({node:  this.parentNode.querySelector('input') , field:  this.parentNode.querySelector('input') , bTime:  true , bSetFocus:  true});">Дата начала факт</label>
                <input class="field" name="FACT_STARTED" type="text"  onclick="BX.calendar({node:  this , field:  this , bTime:  true , bSetFocus:  true});">
            </div>
        </div>

        <div data-event-toggle-prop data-target="TASK,WORK" class="col-6 hidden">
            <div class="legend">
                <label for="FACT_ENDED"  onclick="BX.calendar({node:  this.parentNode.querySelector('input') , field:  this.parentNode.querySelector('input') , bTime:  true , bSetFocus:  true, callback_after: correctTime(this)});">Дата окончания факт</label>
                <input class="field"  name="FACT_ENDED" type="text"  onclick="BX.calendar({node:  this , field:  this , bTime:  true , bSetFocus:  true, callback_after: correctTime(this)});">
            </div>
        </div>

        <div data-event-toggle-prop data-target="EVENT" class="col-6 hidden">
            <div class="legend">
                <label for="PROVIDER">Организатор</label>
                <input class="field" data-user-select-render name="PROVIDER" type="text" required>
            </div>
        </div>

        <div data-event-toggle-prop data-target="TASK" class="col-6 hidden">
            <div class="legend">
                <label for="GUARANTOR">Поручитель</label>
                <input class="field" data-user-select-render name="GUARANTOR" type="text" required>
            </div>
        </div>

        <div data-event-toggle-prop data-target="TASK,WORK" class="col-6 hidden">
            <div class="legend">
                <label for="EXECUTOR">Исполнитель</label>
                <input class="field" data-user-select-render name="EXECUTOR" type="text" required>
            </div>
        </div>



        <div class="col-6">
            <div class="legend">
                <label for="DESCRIPTION">Описание</label>
                <textarea  class="field" name="DESCRIPTION"></textarea>
            </div>
        </div>

        <div class="col-6">
            <div class="legend">
                <label >Материалы</label>

                <input multiple type="file" id="FILES" name="FILES" class="uploadFiles">
            </div>
        </div>
    </div>
</form>