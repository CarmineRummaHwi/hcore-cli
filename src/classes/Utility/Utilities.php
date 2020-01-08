<?php
/**
 * HCORE CLI
 * @author carmine.rumma@healthwareinternational.com
 * @package hcore/cli
 */
namespace hcore\cli;

class Utilities
{
    /**
     * @return bool
     */
    public static function is_cli():bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Pretty Dump an Array
     * @param array $arr
     */
    public static function ppp(array $arr):void
    {
        echo "<pre>";
        print_r($arr);
    }

    /**
     * Pretty Dump an Array and Die
     * @param array $arr
     */
    public static function ddd(array $arr):void
    {
        echo "<pre>";
        print_r($arr);
        die("END");
    }

    /**
     * Check if Composer is installed
     * @return bool
     */
    public static function checkComposerInstalled():bool
    {
        $result = shell_exec('composer -v > /dev/null 2>&1
                                    COMPOSER=$?
                                    if [[ $COMPOSER -ne 0 ]]; then
                                        echo "0"
                                    else
                                        echo "1"
                                    fi');

        return (trim($result) === '0') ? false : true;
    }

    /**
     * Check if NodeJs is installed
     * @return bool
     */
    public static function checkNodeJsInstalled():bool
    {
        $result = shell_exec('node -v > /dev/null 2>&1
                                    NODE=$?
                                    if [[ $NODE -ne 0 ]]; then
                                        echo "0"
                                    else
                                        echo "1"
                                    fi');

        return (trim($result) === '0') ? false : true;
    }

    /**
     * Check if npm is installed
     * @return bool
     */
    public static function checkNPMInstalled():bool
    {
        $result = shell_exec('npm -v > /dev/null 2>&1
                                    NPM=$?
                                    if [[ $NPM -ne 0 ]]; then
                                        echo "0"
                                    else
                                        echo "1"
                                    fi');

        return (trim($result) === '0') ? false : true;
    }

    /**
     * Check if newman is installed
     * @return bool
     */
    public static function checkNewmanInstalled():bool
    {
        $result = shell_exec('newman -v > /dev/null 2>&1
                                    NEWMAN=$?
                                    if [[ $NEWMAN -ne 0 ]]; then
                                        echo "0"
                                    else
                                        echo "1"
                                    fi');

        return (trim($result) === '0') ? false : true;
    }

    /**
     * Check if newman is installed
     * @return bool
     */
    public static function dirIsRepository(string $path):bool
    {
        $result = file_exists($path . "/.git/config");
        return $result;
    }

    /**
     * @param string $arg
     * @param array $longoptions
     * @param bool $get_value
     * @return bool|int
     */
    public static function checkLongOption(string $arg, array $longoptions = array())
    {

        foreach ($longoptions as $opt) {
            if (strpos($arg, $opt) !== FALSE) {
                //$key = strpos($arg, $opt);
                return array(str_replace("--","", $opt), str_replace($opt, "", $arg));
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public static function getOptions(array $longoptions = array()):?array {
        global $argv, $argc;
        $options = array();

        for($i = 0;$i < $argc; $i++) {
            $arg = $argv[$i];
            if(strlen($arg) > 1 && $arg[0] == '-') {
                if($arg[1] == '-' && $i + 1 < $argc) {
                    //$i++;
                    //$options[substr($arg,2, strlen($arg))] = $argv[$i];
                    $check = self::checkLongOption($arg, array_map( function ($item){
                        return "--" . $item;
                    }, $longoptions));

                    if ($check !== false){
                        $options[$check[0]] = $check[1];
                    }
                } else {
                    if ($arg[1] == '-'){

                        $check = self::checkLongOption($arg, array_map( function ($item){
                            return "--" . $item;
                        }, $longoptions));

                        if ($check !== false){
                            $options[$check[0]] = $check[1];
                        }
                        //$options[substr($arg,2, strlen($arg))] = substr($arg,5, strlen($arg));
                    } else {
                        $options[$arg[1]] = substr($arg, 2, strlen($arg));
                    }
                }
            }
        }
        return $options;
    }

    /**
     * @param string $needle
     * @param array $argv
     * @return string|null
     */
    public static function searchArgvValue(string $needle, array $argv): ?string
    {

        // Strict search (ex. -uusername)
        $indexArr = array_search($needle, $argv);
        if ($indexArr !== false) {
            if (isset($argv[$indexArr + 1])) {
                return trim($argv[$indexArr + 1]);
            }
        }


        // Not strict search (ex. -uusername)
        /*
        $ret = array_keys(array_filter($argv, function($var) use ($needle){
            return strpos($var, $needle) !== false;
        }));

        if (count($ret) > 0){
            return trim(str_replace($needle, "", $argv[$ret[0]]));
        }
        */

        return null;
    }

    /**
     * @param string $src
     * @param string $dest
     * @return bool
     */
    public static function copyResource(string $src, string $dest): bool
    {
        if (file_exists($dest)) {
            if (is_dir($dest)) {
                $objects = scandir($dest);
                foreach ($objects as $object) {
                    self::rrmdir($object);
                }
            }
            if (!unlink($dest)) {
                return false;
            }
        }

        if (!copy($src, $dest)) {
            echo $dest . " not created!.\n";
            return false;
        }
        return true;
    }

    /**
     * @param string $source
     * @param string $dest
     */
    public static function copyDirectory(string $source, string $dest): void{
        mkdir($dest, 0755);
        foreach (
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }

    /**
     * @param string $dir
     * @return bool
     */
    public static function rrmdir(string $dir) : bool
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        self::rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            if (rmdir($dir)) {
                return true;
            }

            return false;
        }
    }

    /**
     * @param string $path
     * @param int $permissions
     * @return bool
     */
    public static function createDir(string $path, int $permissions) : bool
    {
        if (is_dir($path)) {
            if (!self::rrmdir($path)) {
                return false;
            }
        }

        if (mkdir($path, 0777, true) === true) {
            $permit = $permissions; // (($writable) ? 0664 : 0444);
            if (!chmod($path, $permit)) {
                return false;
            }
        } else {
            echo "An error is occourred during " . $path . " creation command.\n";
            return false;
        }
        return true;
    }

    /**
     * @param array $arr
     * @return bool|string
     */
    public static function arrayToStringPhp(array $arr)
    {
        $base = "array(\n";
        $keys = array_keys($arr);
        foreach ($keys as $key) {
            $base .= "\t\t\"$key\"" . " => \"" . $arr[$key] . "\",\n";
        }
        $base = substr($base, 0, -2);
        $base .= "\n\t)";

        return $base;
    }

    /**
     * @param string $pattern
     * @param string $content
     * @return bool|string
     */
    public static function getMatch(string $pattern, string $content)
    {
        preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE, 0);
        if (sizeof($matches) > 0) {
            $search = $matches[0][0];
            $index = $matches[0][1];
            return substr($content, $index, strlen($search));
        }
        return $content;
    }

    /**
     * @param string $pattern
     * @param string $content
     * @param string $replace
     * @return mixed|string
     */
    public static function replaceMatch(string $pattern, string $content, string $replace)
    {
        preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE, 0);
        if (sizeof($matches) > 0) {
            $search = $matches[0][0];
            $index = $matches[0][1];
            return substr_replace($content, $replace, $index, strlen($search));
        }
        return $content;
    }
}
