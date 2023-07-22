<?php
namespace Gpi\Workproject\Events;

class OnBeforeProlog
{
     function Listen(){
        self::defineConstants();
        self::InitJsCore();
        self::handleEvents();
     }

     function InitJsCore(){

         global $APPLICATION;

         $APPLICATION->AddHeadScript("/bitrix/js/gpi.workproject/functions.js");

         $arJsConfig = array(
             "toolpicker" => array(
                 "js" => "/bitrix/js/gpi.workproject/toolpicker/script.js",
                 "css" => "/bitrix/js/gpi.workproject/toolpicker/style.css",
                 "rel" => Array("ajax", "popup", "ui.dialogs.messagebox", 'ui.buttons.icons', 'ui.notification', 'ui.bootstrap4'),
                 "skip_core" => false | true,
             ),
             "bootsrap5" => array(
                 "js" => "/bitrix/js/gpi.workproject/boostrap5/js/bootstrap.min.js",
                 "css" => "/bitrix/js/gpi.workproject/boostrap5/css/bootstrap.min.css",
                 "skip_core" => false | true,
             ),
             "jszip" => array(
                 "js" => "/bitrix/js/gpi.workproject/jszip/dist/jszip.js",
             ),
             "debag" => array(
                 "js" => "/bitrix/js/gpi.workproject/events.debag/script.js",
             ),
             "jquery3.6.1" => array(
                 "js" => [
                     '/bitrix/js/gpi.workproject/jquery/jquery-3.6.1.min.js',
                 ],
             ),
             "owl.carousel" => array(
                 "js" => '/bitrix/js/gpi.workproject/owl.carousel/owl.carousel.js',
                 'css' => [
                     '/bitrix/js/gpi.workproject/owl.carousel/assets/owl.carousel.min.css',
                     '/bitrix/js/gpi.workproject/owl.carousel/assets/owl.theme.default.min.css',
                 ],
                 "rel" => Array("jquery3.6.1"),
             ),
             "selectize" => array(
                 "js" => '/bitrix/js/gpi.workproject/selectize/selectize.js',
                 'css' => [
                     '/bitrix/js/gpi.workproject/selectize/selectize.css',
                 ],
             ),
             "fancybox" => array(
                 "js" => '/bitrix/js/gpi.workproject/fancybox/jquery.fancybox.min.js',
                 'css' => [
                     '/bitrix/js/gpi.workproject/fancybox/jquery.fancybox.min.css',
                 ],
             ),
             'sidepanel.reference.link.save' => array(
                 "js" => '/bitrix/js/gpi.workproject/sidepanel.reference.link.save/script.js',
             ),
             'ionicon' => array(
                 "js" => [
                     '/bitrix/js/gpi.workproject/ionicon/ionicons.esm.js',
                     '/bitrix/js/gpi.workproject/ionicon/ionicons.js',
                 ],
             ),
             'slimselect' => array(
                 "css" => [
                     '/bitrix/js/gpi.workproject/slimselect/slimselect.css',
                 ],
                 'js' => [
                     '/bitrix/js/gpi.workproject/slimselect/slimselect.min.js',
                 ]
             ),
             "bear.file.input" => array(
                 "js" => [
                     '/bitrix/js/gpi.workproject/bear.file.input/script.js'
                 ],
                 'css' => [
                     '/bitrix/js/gpi.workproject/bear.file.input/style.css'
                 ],
             ),
             "rs.buttons" => array(
                 'css' => [
                     '/bitrix/js/gpi.workproject/buttons/style.css'
                 ],
             ),
             "ui.list" => [
                 'css' => [
                     '/bitrix/js/gpi.workproject/ui.list/style.css',
                 ]
             ],
             "cool.editor" => [
                 'css' => [
                     '/bitrix/js/gpi.workproject/cool.editor/style.css',
                 ],
                 'js' => [
                     '/bitrix/js/gpi.workproject/cool.editor/script.js',
                 ]
             ],
         );

         if(MAIN_USER_THEME == 'WHITE'){
             $arJsConfig["ui.list"]['css'][]    = '/bitrix/js/gpi.workproject/ui.list/white_theme.css';
             $arJsConfig["rs.buttons"]['css'][] = '/bitrix/js/gpi.workproject/buttons/white_theme.css';
         }

         foreach ($arJsConfig as $ext => $arExt) {
             \CJSCore::RegisterExt($ext, $arExt);
         }

     }

     function defineConstants(){
         $session = \Bitrix\Main\Application::getInstance()->getSession();


         global $USER;
         $userData = \Bitrix\Main\UserTable::getList([
             'select' => ['UF_SHOW_COMPACT_NOTES_RS', 'UF_THEME_RS'],
             'filter' => ['ID' => $USER->getId()],
         ])->fetch();


         if(!$userData['UF_THEME_RS'])
             $userData['UF_THEME_RS'] = $session['UF_THEME_RS'];

         if(!$userData['UF_SHOW_COMPACT_NOTES_RS'])
             $userData['UF_SHOW_COMPACT_NOTES_RS'] = $session['UF_SHOW_COMPACT_NOTES_RS'];

         define('MAIN_USER_THEME', $userData['UF_THEME_RS']);
         define('SHOW_COMPACT_NOTES', $userData['UF_SHOW_COMPACT_NOTES_RS']);
     }

    function handleEvents(){

        AddEventHandler("main", "OnBeforeSiteAdd", ['Gpi\Workproject\Events\OnBeforeSiteAdd', 'Listen']);
        AddEventHandler("main", "OnSiteDelete", ['Gpi\Workproject\Events\OnSiteDelete', 'Listen']);
    }
}