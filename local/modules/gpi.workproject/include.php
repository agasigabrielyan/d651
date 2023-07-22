<?php

use Bitrix\Main\Loader;

if(1==2)
    spl_autoload_register(function ($class) {
        if(strpos($class, 'Gpi') === false)
            return;

        $class = str_replace( 'Gpi', '', $class);
        $folders = array_values(array_filter(explode('\\', $class)));
        $fileName = $folders[count($folders)-1].'.php';
        unset($folders[count($folders)-1]);
        $tryPath = __DIR__.'\\classes\\'.implode('\\', $folders).'\\'.$fileName;

        if(file_exists($tryPath))
            include_once $tryPath;
    });
else
    spl_autoload_register(function ($class) {
        if(strpos($class, 'Gpi') === false)
            return;

        $class = str_replace( 'Gpi', '', $class);
        $folders = array_values(array_filter(explode('\\', $class)));
        $fileName = $folders[count($folders)-1].'.php';
        unset($folders[count($folders)-1]);
        $tryPath = __DIR__.'/classes/'.implode('/', $folders).'/'.$fileName;

        if(file_exists($tryPath))
            include_once $tryPath;
    });