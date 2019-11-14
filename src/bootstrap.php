<?php
/**
 * Created by PhpStorm.
 * User: crumma
 * Date: 2019-11-14
 * Time: 14:38
 */

if (!function_exists("console")){
    $console = new \hcore\cli\Console();
    /**
     * @return \hcore\cli\Console
     */
    function console(){
        global $console;
        return $console;
    }
}

if (!function_exists("ppp")){
    function ppp(array $arr){
        return \hcore\cli\Utilities::ppp($arr);
    }
}

if (!function_exists("ddd")){
    function ddd(array $arr){
        return \hcore\cli\Utilities::ddd($arr);
    }
}

if (!function_exists("search_argv_val")){
    function search_argv_val(string $needle, array $argv){
        return \hcore\cli\Utilities::searchArgvValue($needle, $argv);
    }
}
