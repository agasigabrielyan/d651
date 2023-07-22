<div class="phonebook">
    <div class="block-title">
        <span>Телефонный справочник</span>

        <input type="text"  oninput="getContactRequset(this)">
        <div class="pencil">
            <svg fill="#FFFFFF" width="800px" height="800px" viewBox="0 0 64 64" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"> <rect id="Icons" x="-128" y="-64" width="1280" height="800" style="fill:none;"/> <g id="Icons1" serif:id="Icons"> <g id="Strike"> </g> <g id="H1"> </g> <g id="H2"> </g> <g id="H3"> </g> <g id="list-ul"> </g> <g id="hamburger-1"> </g> <g id="hamburger-2"> </g> <g id="list-ol"> </g> <g id="list-task"> </g> <g id="trash"> </g> <g id="vertical-menu"> </g> <g id="horizontal-menu"> </g> <g id="sidebar-2"> </g> <g id="Pen"> <path hidden d="M56.009,51.832l0,4.2l-33.652,-0.026l4.709,-4.174l28.943,0Z" style="fill-rule:nonzero;"/> <path d="M48.453,8.119c1.65,0 2.506,0.129 4.753,2.011c2.294,1.922 2.707,3.42 2.803,5.088c0.102,1.795 -0.504,3.975 -2.188,5.681l-31.961,31.961c-0.52,0.475 -0.63,0.449 -0.977,0.553l-10.226,2.556c-1.472,0.299 -2.854,-1.049 -2.55,-2.549l2.557,-10.226c0.1,-0.334 0.133,-0.517 0.553,-0.977c10.696,-10.696 21.195,-21.593 32.09,-32.087c1.421,-1.335 3.497,-2.011 5.146,-2.011Zm0,4.143c-0.86,0.016 -1.698,0.371 -2.311,0.976l-31.54,31.541l-1.566,6.261l6.262,-1.565c10.544,-10.544 21.419,-20.768 31.63,-31.634c1.674,-1.825 0.444,-5.453 -2.306,-5.577c-0.056,-0.002 -0.112,-0.003 -0.169,-0.002Z" style="fill-rule:nonzero;"/> </g> <g id="Pen1" serif:id="Pen"> </g> <g id="clock"> </g> <g id="external-link"> </g> <g id="hr"> </g> <g id="info"> </g> <g id="warning"> </g> <g id="plus-circle"> </g> <g id="minus-circle"> </g> <g id="vue"> </g> <g id="cog"> </g> <g id="logo"> </g> <g id="radio-check"> </g> <g id="eye-slash"> </g> <g id="eye"> </g> <g id="toggle-off"> </g> <g id="shredder"> </g> <g id="spinner--loading--dots-" serif:id="spinner [loading, dots]"> </g> <g id="react"> </g> <g id="check-selected"> </g> <g id="turn-off"> </g> <g id="code-block"> </g> <g id="user"> </g> <g id="coffee-bean"> </g> <g id="coffee-beans"> <g id="coffee-bean1" serif:id="coffee-bean"> </g> </g> <g id="coffee-bean-filled"> </g> <g id="coffee-beans-filled"> <g id="coffee-bean2" serif:id="coffee-bean"> </g> </g> <g id="clipboard"> </g> <g id="clipboard-paste"> </g> <g id="clipboard-copy"> </g> <g id="Layer1"> </g> </g> </svg>
        </div>
    </div>

    <div class="phonebook-result" hidden>
        <div class="row">
            <div class="col-4">Фио</div>
            <div class="col-2">Номер</div>
            <div class="col-3">Должность</div>
            <div class="col-3">Подразделение</div>
        </div>

        <div class="row">
            <div class="col-4">Иванов<br> Иван Иванович</div>
            <div class="col-2">20505</div>
            <div class="col-3">Начальник департамента</div>
            <div class="col-3">Департамент 651</div>
        </div>

        <div class="row">
            <div class="col-4">Чуваков<br> Чувак Братанович</div>
            <div class="col-2">39050</div>
            <div class="col-3">Старший разработчик</div>
            <div class="col-3">Департамента 555</div>
        </div>
    </div>

    <div class="phonebook-result goost-result">
        <div class="row">
            <div class="col-4">Фио</div>
            <div class="col-2">Номер</div>
            <div class="col-3">Должность</div>
            <div class="col-3">Подразделение</div>
        </div>

        <div class="row">
            <div class="col-4 goost-head"><div class="goost"></div><div class="goost"></div><div class="goost"></div></div>
            <div class="col-2 goost-head"><div class="goost"></div></div>
            <div class="col-3 goost-head"><div class="goost"></div><div class="goost"></div></div>
            <div class="col-3 goost-head"><div class="goost"></div></div>
        </div>

        <div class="row">
            <div class="col-4 goost-head"><div class="goost"></div><div class="goost"></div><div class="goost"></div></div>
            <div class="col-2 goost-head"><div class="goost"></div></div>
            <div class="col-3 goost-head"><div class="goost"></div><div class="goost"></div></div>
            <div class="col-3 goost-head"><div class="goost"></div></div>
        </div>

        <div class="row">
            <div class="col-4 goost-head"><div class="goost"></div><div class="goost"></div><div class="goost"></div></div>
            <div class="col-2 goost-head"><div class="goost"></div></div>
            <div class="col-3 goost-head"><div class="goost"></div><div class="goost"></div></div>
            <div class="col-3 goost-head"><div class="goost"></div></div>
        </div>

        <div class="row">
            <div class="col-4 goost-head"><div class="goost"></div><div class="goost"></div><div class="goost"></div></div>
            <div class="col-2 goost-head"><div class="goost"></div></div>
            <div class="col-3 goost-head"><div class="goost"></div><div class="goost"></div></div>
            <div class="col-3 goost-head"><div class="goost"></div></div>
        </div>
    </div>
</div>
