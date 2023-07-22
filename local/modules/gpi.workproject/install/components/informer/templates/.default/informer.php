<?php
use Bitrix\Main\Context;
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
?>
<script>
    window.active_informer = '<?=$arResult['TARGET']?>';
</script>

<div class="informer">
    <div class="sidebar">
        <div class="notes">
            <div class="informer-header">
                <ul class="light-menu">
                    <li data-link="NOTES">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#000000" width="800px" height="800px" viewBox="0 0 32 32" id="Outlined">
                            <title/>
                            <g id="Fill">
                                <path d="M25,26a1,1,0,0,1-1,1H8a1,1,0,0,1-1-1V5H17V3H5V26a3,3,0,0,0,3,3H24a3,3,0,0,0,3-3V13H25Z"/>
                                <path d="M27.12,2.88a3.08,3.08,0,0,0-4.24,0L17,8.75,16,14.05,21.25,13l5.87-5.87A3,3,0,0,0,27.12,2.88Zm-6.86,8.27-1.76.35.35-1.76,3.32-3.33,1.42,1.42Zm5.45-5.44-.71.7L23.59,5l.7-.71h0a1,1,0,0,1,1.42,0A1,1,0,0,1,25.71,5.71Z"/>
                            </g>
                        </svg>
                    </li>
                    <li data-link="PROJECT">
                        <svg fill="#000000" width="800px" height="800px" viewBox="0 0 36 36" version="1.1"  preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <title>briefcase-line</title>
                            <path d="M32,28a0,0,0,0,1,0,0H4V21.32a7.1,7.1,0,0,1-2-1.43V28a2,2,0,0,0,2,2H32a2,2,0,0,0,2-2V19.89a6.74,6.74,0,0,1-2,1.42Z" class="clr-i-outline clr-i-outline-path-1"></path><path d="M25,22.4a1,1,0,0,0,1-1V15.94H24V18H14v2H24v1.4A1,1,0,0,0,25,22.4Z" class="clr-i-outline clr-i-outline-path-2"></path><path d="M33,6H24V4.38A2.42,2.42,0,0,0,21.55,2h-7.1A2.42,2.42,0,0,0,12,4.38V6H3A1,1,0,0,0,2,7v8a5,5,0,0,0,5,5h3v1.4a1,1,0,0,0,2,0V15.94H10V18H7a3,3,0,0,1-3-3V8H32v7a3,3,0,0,1-3,3H28v2h1a5,5,0,0,0,5-5V7A1,1,0,0,0,33,6ZM22,6H14V4.43A.45.45,0,0,1,14.45,4h7.11a.43.43,0,0,1,.44.42Z" class="clr-i-outline clr-i-outline-path-3"></path>
                            <rect x="0" y="0" width="36" height="36" fill-opacity="0"/>
                        </svg>
                    </li>
                    <li data-link="TASKS">
                        <svg fill="#000000" width="800px" height="800px" viewBox="0 0 36 36" version="1.1"  preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <title>tasks-line</title>
                            <path class="clr-i-outline clr-i-outline-path-1" d="M29.29,34H6.71A1.7,1.7,0,0,1,5,32.31V6.69A1.75,1.75,0,0,1,7,5H9V7H7V32H29V7H27V5h2.25A1.7,1.7,0,0,1,31,6.69V32.31A1.7,1.7,0,0,1,29.29,34Z"></path><path class="clr-i-outline clr-i-outline-path-2" d="M16.66,25.76,11.3,20.4A1,1,0,0,1,12.72,19l3.94,3.94,8.64-8.64a1,1,0,0,1,1.41,1.41Z"></path><path class="clr-i-outline clr-i-outline-path-3" d="M26,11H10V7.33A2.34,2.34,0,0,1,12.33,5h1.79a4,4,0,0,1,7.75,0h1.79A2.34,2.34,0,0,1,26,7.33ZM12,9H24V7.33A.33.33,0,0,0,23.67,7H20V6a2,2,0,0,0-4,0V7H12.33a.33.33,0,0,0-.33.33Z"></path>
                            <rect x="0" y="0" width="36" height="36" fill-opacity="0"/>
                        </svg>
                    </li>
                    <li data-link="CALENDAR">
                        <svg fill="#000000" width="800px" height="800px" viewBox="0 0 122.88 122.88" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="enable-background:new 0 0 122.88 122.88" xml:space="preserve">
                            <g>
                                <path d="M81.61,4.73c0-2.61,2.58-4.73,5.77-4.73c3.19,0,5.77,2.12,5.77,4.73v20.72c0,2.61-2.58,4.73-5.77,4.73 c-3.19,0-5.77-2.12-5.77-4.73V4.73L81.61,4.73z M66.11,103.81c-0.34,0-0.61-1.43-0.61-3.2c0-1.77,0.27-3.2,0.61-3.2H81.9 c0.34,0,0.61,1.43,0.61,3.2c0,1.77-0.27,3.2-0.61,3.2H66.11L66.11,103.81z M15.85,67.09c-0.34,0-0.61-1.43-0.61-3.2 c0-1.77,0.27-3.2,0.61-3.2h15.79c0.34,0,0.61,1.43,0.61,3.2c0,1.77-0.27,3.2-0.61,3.2H15.85L15.85,67.09z M40.98,67.09 c-0.34,0-0.61-1.43-0.61-3.2c0-1.77,0.27-3.2,0.61-3.2h15.79c0.34,0,0.61,1.43,0.61,3.2c0,1.77-0.27,3.2-0.61,3.2H40.98 L40.98,67.09z M66.11,67.09c-0.34,0-0.61-1.43-0.61-3.2c0-1.77,0.27-3.2,0.61-3.2H81.9c0.34,0,0.61,1.43,0.61,3.2 c0,1.77-0.27,3.2-0.61,3.2H66.11L66.11,67.09z M91.25,67.09c-0.34,0-0.61-1.43-0.61-3.2c0-1.77,0.27-3.2,0.61-3.2h15.79 c0.34,0,0.61,1.43,0.61,3.2c0,1.77-0.27,3.2-0.61,3.2H91.25L91.25,67.09z M15.85,85.45c-0.34,0-0.61-1.43-0.61-3.2 c0-1.77,0.27-3.2,0.61-3.2h15.79c0.34,0,0.61,1.43,0.61,3.2c0,1.77-0.27,3.2-0.61,3.2H15.85L15.85,85.45z M40.98,85.45 c-0.34,0-0.61-1.43-0.61-3.2c0-1.77,0.27-3.2,0.61-3.2h15.79c0.34,0,0.61,1.43,0.61,3.2c0,1.77-0.27,3.2-0.61,3.2H40.98 L40.98,85.45z M66.11,85.45c-0.34,0-0.61-1.43-0.61-3.2c0-1.77,0.27-3.2,0.61-3.2H81.9c0.34,0,0.61,1.43,0.61,3.2 c0,1.77-0.27,3.2-0.61,3.2H66.11L66.11,85.45z M91.25,85.45c-0.34,0-0.61-1.43-0.61-3.2c0-1.77,0.27-3.2,0.61-3.2h15.79 c0.34,0,0.61,1.43,0.61,3.2c0,1.77-0.27,3.2-0.61,3.2H91.25L91.25,85.45z M15.85,103.81c-0.34,0-0.61-1.43-0.61-3.2 c0-1.77,0.27-3.2,0.61-3.2h15.79c0.34,0,0.61,1.43,0.61,3.2c0,1.77-0.27,3.2-0.61,3.2H15.85L15.85,103.81z M40.98,103.81 c-0.34,0-0.61-1.43-0.61-3.2c0-1.77,0.27-3.2,0.61-3.2h15.79c0.34,0,0.61,1.43,0.61,3.2c0,1.77-0.27,3.2-0.61,3.2H40.98 L40.98,103.81z M29.61,4.73c0-2.61,2.58-4.73,5.77-4.73s5.77,2.12,5.77,4.73v20.72c0,2.61-2.58,4.73-5.77,4.73 s-5.77-2.12-5.77-4.73V4.73L29.61,4.73z M6.4,45.32h110.07V21.47c0-0.8-0.33-1.53-0.86-2.07c-0.53-0.53-1.26-0.86-2.07-0.86H103 c-1.77,0-3.2-1.43-3.2-3.2c0-1.77,1.43-3.2,3.2-3.2h10.55c2.57,0,4.9,1.05,6.59,2.74c1.69,1.69,2.74,4.02,2.74,6.59v27.06v65.03 c0,2.57-1.05,4.9-2.74,6.59c-1.69,1.69-4.02,2.74-6.59,2.74H9.33c-2.57,0-4.9-1.05-6.59-2.74C1.05,118.45,0,116.12,0,113.55V48.52 V21.47c0-2.57,1.05-4.9,2.74-6.59c1.69-1.69,4.02-2.74,6.59-2.74H20.6c1.77,0,3.2,1.43,3.2,3.2c0,1.77-1.43,3.2-3.2,3.2H9.33 c-0.8,0-1.53,0.33-2.07,0.86c-0.53,0.53-0.86,1.26-0.86,2.07V45.32L6.4,45.32z M116.48,51.73H6.4v61.82c0,0.8,0.33,1.53,0.86,2.07 c0.53,0.53,1.26,0.86,2.07,0.86h104.22c0.8,0,1.53-0.33,2.07-0.86c0.53-0.53,0.86-1.26,0.86-2.07V51.73L116.48,51.73z M50.43,18.54 c-1.77,0-3.2-1.43-3.2-3.2c0-1.77,1.43-3.2,3.2-3.2h21.49c1.77,0,3.2,1.43,3.2,3.2c0,1.77-1.43,3.2-3.2,3.2H50.43L50.43,18.54z" />
                            </g>
                        </svg>
                    </li>
                    <div id="marker"></div>
                </ul>
            </div>
            <div class="targets">
                <div class="target projects<?=$arResult['TARGET'] == 'PROJECT'? ' active':''?>" id="PROJECT">

                    <?if($arResult['TARGET'] == 'PROJECT'):?>
                        <?$APPLICATION->IncludeComponent('rs.notes', '', ['SEF_FOLDER' => '/notes/'])?>
                    <?endif;?>

                </div>

                <div class="target notes<?=$arResult['TARGET'] == 'NOTES'? ' active':''?>" id="NOTES">

                    <?if($arResult['TARGET'] == 'NOTES'):?>
                        <?$APPLICATION->IncludeComponent('rs:notes', '', ['SEF_FOLDER' => '/notes/'])?>
                    <?endif;?>

                </div>

                <div class="target tasks<?=$arResult['TARGET'] == 'TASKS'? ' active':''?>" id="TASKS">
                    <?if($arResult['TARGET'] == 'TASKS'):?>
                        <?$APPLICATION->IncludeComponent('rs.notes', '', ['SEF_FOLDER' => '/notes/'])?>
                    <?endif;?>
                </div>

                <div class="target calendar<?=$arResult['TARGET'] == 'CALENDAR'? ' active':''?>" id="CALENDAR">
                    <?if($arResult['TARGET'] == 'CALENDAR'):?>
                        <?$APPLICATION->IncludeComponent('rs.notes', '', ['SEF_FOLDER' => '/notes/'])?>
                    <?endif;?>
                </div>

            </div>
        </div>
    </div>
</div>


