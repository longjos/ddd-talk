<?php

require("../vendor/autoload.php");

function fohAutoLoader($className)
{
     $file = dirname(__DIR__)
        .DIRECTORY_SEPARATOR
         .'foh'
         .DIRECTORY_SEPARATOR
        .'domain'
        .DIRECTORY_SEPARATOR
        .str_replace('\\', DIRECTORY_SEPARATOR, $className)
        .'.php';
    if (file_exists($file)) {
        include $file;
    }
}

spl_autoload_register('fohAutoLoader');


require("controllers.php");
