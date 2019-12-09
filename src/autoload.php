<?php
/**
 * HCORE CLI
 * @author carmine.rumma@healthwareinternational.com
 * @package hcore/cli
 */

define ("CLASSES_PATH", dirname(__FILE__) . "/classes");
define ("DOC_ROOT",     dirname(__FILE__));

spl_autoload_register(function (string $class):bool {

    // Support for namespaces
    if (strpos($class, '\\') !== false) {
        $parts = explode('\\', $class);
        $class = end($parts); // . '.php';
    }

    if (file_exists(CLASSES_PATH . "/" . $class . ".php")) {
        require CLASSES_PATH . "/" . $class . ".php";
        return true;
    }

    if (file_exists(CLASSES_PATH . "/Utility/" . $class . ".php")) {
        require CLASSES_PATH . "/Utility/" . $class . ".php";
        return true;
    }

    if (file_exists(CLASSES_PATH . "/Console/" . $class . ".php")) {
        require CLASSES_PATH . "/Console/" . $class . ".php";
        return true;
    }

    if (file_exists(CLASSES_PATH . "/Console/CLI/" . $class . ".php")) {
        require CLASSES_PATH . "/Console/CLI/" . $class . ".php";
        return true;
    }
    return false;
});
