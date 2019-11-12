<?php


namespace hcore\cli;


class Utilities
{

    public static function ppp(array $arr):void {
        echo "<pre>";
        print_r($arr);
    }

    public static function ddd(array $arr):void {
        echo "<pre>";
        print_r($arr);
        die("END");
    }

    public static function copyResource(string $src, string $dest): bool
    {
        if (file_exists($dest)) {
            if(is_dir($dest)){
                $objects = scandir($dest);
                foreach ($objects as $object) {
                    self::rrmdir($object);
                }
            }
            if(!unlink($dest)) {
                return false;
            }
        }

        if (!copy($src, $dest)) {
            echo $dest . " not created!.\n";
            return false;
        }
        return true;
    }

    public static function rrmdir(string $dir) : bool
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") {
                        self::rrmdir($dir . "/" . $object);
                    }else {
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            if(rmdir($dir)){
                return true;
            }

            return false;
        }
    }

    public static function createDir(string $path, int $permissions) : bool
    {
        if (is_dir($path)) {
            if(!self::rrmdir($path)){
                return false;
            }
        }

        if (mkdir($path, 0777, true) === true) {
            $permit = $permissions; // (($writable) ? 0664 : 0444);
            if(!chmod($path, $permit)){
                return false;
            }
        } else {
            echo "An error is occourred during " . $path . " creation command.\n";
            return false;
        }
        return true;
    }

    public static function arrayToStringPhp(array $arr) {
        $base = "array(\n";
        $keys = array_keys($arr);
        foreach ($keys as $key){
            $base .= "\t\t\"$key\"" . " => \"" . $arr[$key] . "\",\n";
        }
        $base = substr($base, 0, -2);
        $base .= "\n\t)";

        return $base;
    }

    public static function getMatch(string $pattern, string $content) {
        preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE, 0);
        if(sizeof($matches) > 0) {
            $search = $matches[0][0];
            $index = $matches[0][1];
            return substr($content, $index, strlen($search));
        }
        return $content;
    }

    public static function replaceMatch(string $pattern, string $content, string $replace) {
        preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE, 0);
        if(sizeof($matches) > 0) {
            $search = $matches[0][0];
            $index = $matches[0][1];
            return substr_replace($content, $replace, $index, strlen($search));
        }
        return $content;
    }
}
