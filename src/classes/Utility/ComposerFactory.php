<?php
/**
 * Created by PhpStorm.
 * User: crumma
 * Date: 2019-09-19
 * Time: 16:13
 */

namespace hcore\cli;

class ComposerFactory
{

    /* @var array */
    private $root;
    /* @var array */
    public $authors;

    public function __construct()
    {
        $this->root = array();
    }

    public function add(string $key, string $value):void
    {
        $this->root[$key] = $value;
    }

    public function addAuthor(string $value):void
    {
        $this->root["authors"][] = $value;
    }

    public function addRequire(string $dep, string $ver):void
    {
        $this->root["require"][$dep] = $ver;
    }

    public function addRequireDev(string $value):void
    {
        $this->root["require-dev"][] = $value;
    }

    public function addRepository(array $value):void
    {
        $this->root["repositories"][] = $value;
    }
    public function addAutoload(string $key, string $value):void
    {
        $this->root["autoload"][$key] = $value;
    }
    public function addScripts():void
    {
        $this->root["scripts"] = [
            "post-update-cmd" => ["\\hcore\\Installer::setup"],
            "post-autoload-dump" => ["\\hcore\\Installer::dumpautoload"],
            "create-project-tree" => ["\\hcore\\Installer::createprojecttree"],
            "build-preflights" => ["\\hcore\\Installer::buildpreflights"]
        ];
    }

    public function toJson():string
    {
        return str_replace("\/", "/", json_encode($this->root, JSON_PRETTY_PRINT));
    }
}
