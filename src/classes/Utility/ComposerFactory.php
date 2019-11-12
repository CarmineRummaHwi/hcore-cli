<?php
/**
 * Created by PhpStorm.
 * User: crumma
 * Date: 2019-09-19
 * Time: 16:13
 */

namespace hcore\cli;

class ComposerFactory {

    private $root;
    public  $authors;

    public function __construct()
    {
        $this->root = array();
    }

    public function add($key, $value){
        $this->root[$key] = $value;
    }

    public function addAuthor($value){
        $this->root["authors"][] = $value;
    }

    public function addRequire($dep, $ver){
        $this->root["require"][$dep] = $ver;
    }

    public function addRequireDev($value){
        $this->root["require-dev"][] = $value;
    }

    public function addRepository($value){
        $this->root["repositories"][] = $value;
    }
    public function addAutoload($key, $value){
        $this->root["autoload"][$key] = $value;
    }
    public function addScripts(){
        $this->root["scripts"] = [
            "post-update-cmd" => ["\\hcore\\Installer::setup"],
            "post-autoload-dump" => ["\\hcore\\Installer::dumpautoload"],
            "create-project-tree" => ["\\hcore\\Installer::createprojecttree"],
            "build-preflights" => ["\\hcore\\Installer::buildpreflights"]
        ];
    }

    public function toJson(){
        return str_replace("\/", "/", json_encode($this->root, JSON_PRETTY_PRINT));
    }
}
