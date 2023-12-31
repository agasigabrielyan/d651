<!DOCTYPE html>
<html>
<head>
    <?
    $APPLICATION->ShowHead();
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/src/lib/widget.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/src/lib/style.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/src/lib/fonts.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/src/lib/gridstack/gridstack-extra.min.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/src/lib/bootstrap.css');
    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/src/lib/gridstack/gridstack.css');?>
    <title><?$APPLICATION->ShowTitle();?></title>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
</head>
<body>
    <div class="hat">
        <div class="hat__inner">
            <div class="hat__not-edit">
                <div class="hat-info">
                    <div class="hat-info__logo d-flex" style="align-items: center;">
                        <div class="upper__avatar" style="background-image:url('<?=SITE_TEMPLATE_PATH?>/src/images/burusking.jpg');width: 55rem;height: 54rem;"></div>
                        <a href="/">Иванов И.И.</a>
                    </div>

                    <div class="hat-info__user">

						<a href="/phonebook/" style="margin-right:20px;" class="hat-info__user_link">
                           <svg xmlns="http://www.w3.org/2000/svg" width="30" height="31" viewBox="0 0 30 31" fill="none">
								<g clip-path="url(#clip0_3_141)">
									<path d="M27.2074146,24.3261738c-0.0010185,0.0010185-0.002039,0.0021324-0.0031509,0.0031509v-0.0078754
				l-1.2020245,1.1940556c-1.5545425,1.5741901-3.8185692,2.2218628-5.9704647,1.7080994
				c-2.1680183-0.5803013-4.2290058-1.5040398-6.1048346-2.7360878c-1.7427559-1.1138039-3.3578119-2.4158192-4.8158855-3.8827877
				c-1.3415875-1.3317642-2.5468559-2.7940998-3.5981059-4.365139c-1.1498528-1.6904898-2.0599666-3.5320311-2.7044873-5.4722672
				c-0.7388594-2.2793169-0.1265872-4.7803917,1.5816016-6.46069l1.4075675-1.4075677
				c0.3913469-0.3931067,1.0272498-0.3944967,1.4202642-0.0031505c0.0010195,0.0010195,0.002131,0.002039,0.0031505,0.0031505
				l4.4441848,4.4441862c0.3931074,0.3913469,0.3944979,1.0272493,0.0031509,1.4202642
				c-0.0010195,0.0010195-0.002039,0.002039-0.0031509,0.0031509l-2.6095934,2.6095934
				c-0.7487755,0.7406206-0.842927,1.917532-0.2213879,2.7677813c0.9438457,1.2953453,1.9883308,2.5142345,3.1236343,3.6455517
				c1.2657824,1.2712498,2.6418419,2.427681,4.1120539,3.4557648c0.8495064,0.5925331,2.0012131,0.4926357,2.7360878-0.237236
				l2.5225754-2.5621471c0.391346-0.3931065,1.0272484-0.394495,1.4202633-0.0031509
				c0.0010185,0.0010185,0.002039,0.002039,0.0031509,0.0031509l4.4521542,4.4600315
				C27.5973721,23.2972584,27.5987606,23.9330673,27.2074146,24.3261738z" fill="#0079C2"></path>
								</g>
								<defs>
									<clipPath id="clip0_3_141">
										<rect width="30" height="30" fill="white" transform="translate(0 0.5)"></rect>
									</clipPath>
								</defs>
							</svg>

                        </a>


                        <a href="https://portal.adm.gazprom.ru/cpgp/home.php" class="hat-info__user_link">
                            <span class="hat-info__my-env">Портал</span>
                            <span class="hat-info__arrow-wrapper"></span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="hat__edit">
                <!-- подгружаются кнопки управления виджетами -->
            </div>
        </div>
        <div class="hat__shadow"></div>
    </div>
    <div class="header">
        <div class="header__inner">
            <div class="header__widgets">
                <?
                $APPLICATION->IncludeComponent('rs:department',
                    'preview',
                    []
                );
                ?>
                <div class="header__widget widget_six-cols">

                    <div class="header__tags">
                        <button class="header__tag">
                            #тэг
                        </button>
                        <button class="header__tag">
                            #тэг
                        </button>
                        <button class="header__tag">
                            #тэг
                        </button>
                    </div>

                    <div class="phone-book">
                        <div class="phone-book__upper">
                            <div class="phone-book__search">
                                <input type="text" placeholder="поиск" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-slider">
        <div class="header-slider__inner">
            <div class="header-slider__banner">
                <a href="#" class="info_report">
                    Интерактивный годовой отчет 2022
                </a>
            </div>
            <div class="header-slider__banner">
                <a href="#" class="info_report info_report1">
                    Новый сервис<br/> <span>“Забронировать переговорную”</span>
                </a>
            </div>
            <div class="header-slider__banner">
                <a href="#" class="info_report info_report2">
                    Опроc:<br/> <span>“Обучение для личного развития”</span>
                </a>
            </div>
            <div class="header-slider__banner">
                <a href="#" class="info_report info_report3">
                    Глоссарий цифровой трансформации
                </a>
            </div>
            <div class="header-slider__banner">
                <a href="#" class="info_report info_report4">
                    Отраслевое совещание Департамента 651
                </a>
            </div>
        </div>
    </div>
    <div class="portal-grid">
        <div class="portal-grid__inner">
            <?$APPLICATION->IncludeComponent('rs:industrial.services', 'preview', [])?>
			<?if($_SERVER['SCRIPT_NAME'] != '' && $_SERVER['SCRIPT_NAME'] != '/' && $_SERVER['SCRIPT_NAME'] != '/index.php'):?>
				<a style="margin-bottom:20px;display: block;" href="/" class="hat-info__user_link">
					<span style="color:#fff;" class="hat-info__my-env">На главную</span>
					<!--span class="hat-info__arrow-wrapper"></span-->
				</a>
			<?endif;?>
            <div id="simple-grid" class="grid-stack">