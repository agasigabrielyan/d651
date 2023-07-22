<?php
/** @var TYPE_NAME $APPLICATION */
/** @var TYPE_NAME $templateFolder */
switch (MAIN_USER_THEME) {
    case 'WHITE':
        $APPLICATION->SetAdditionalCSS($templateFolder . '/white_theme.css');
        break;
}
?>