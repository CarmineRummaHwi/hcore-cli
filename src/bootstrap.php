<?php
/**
 * HCORE CLI
 * @author carmine.rumma@healthwareinternational.com
 * @package hcore/cli
 */

//if (!function_exists("console")){
    $console = new \hcore\cli\Console();
    /**
     * @return \hcore\cli\Console
     */
    function console():\hcore\cli\Console{
        global $console;
        return $console;
    }
//}

if (!function_exists("ppp")){
    function ppp(array $arr):string {
        return \hcore\cli\Utilities::ppp($arr);
    }
}

if (!function_exists("ddd")){
    function ddd(array $arr):string {
        return \hcore\cli\Utilities::ddd($arr);
    }
}

if (!function_exists("search_argv_val")){
    function search_argv_val(string $needle, array $argv): ?string{
        return \hcore\cli\Utilities::searchArgvValue($needle, $argv);
    }
}

/**
 * Check is CLI
 * @return bool
 */
function is_cli():bool{
    return \hcore\cli\Utilities::is_cli();
}
