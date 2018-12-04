<?php

$MAIN_DIR = "Kanba";

$MAIN_PATH = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $MAIN_DIR . DIRECTORY_SEPARATOR;

require_once $MAIN_PATH . "Configurator.php";
require_once $MAIN_PATH . "User.php";
require_once $MAIN_PATH . "Kanba.php";
require_once $MAIN_PATH . "MyPDO.php";
require_once $MAIN_PATH . "Todo.php";
require_once $MAIN_PATH . "TodoList.php";

class Loader {
    public static function resolvePath($path) {
        return $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "Kanba" . DIRECTORY_SEPARATOR . $path;
    }
}