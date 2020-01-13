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

    /**
     * @param string $filename
     */
    public function read(string $filename):void {
        if (file_exists($filename)) {
            $file = file_get_contents($filename);
            $arr = json_decode($file, true);
            $this->root = $arr;
        }
    }

    public function dump(): void{
        ddd($this->root);
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

    public function checkRequireDep(string $dep):bool
    {
        return isset($this->root["require"][$dep]);
    }

    public function addRequireDev(string $dep, string $ver):void
    {
        $this->root["require-dev"][$dep] = $ver;
    }

    public function addRepository(array $value):void
    {
        $exists = false;
        if (isset($this->root["repositories"])) {
            foreach ($this->root["repositories"] as $item) {
                if ($item["url"] === $value["url"]) {
                    $exists = true;
                    break;
                }
            }
        }

        if (!$exists)
            $this->root["repositories"][] = $value;
    }
    public function addAutoload(string $key, array $value):void
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

    public function addConfig(){
        $this->root["config"] = [
            "optimize-autoloader" => true,
            "classmap-authoritative" => true,
            "apcu-autoloader" => true
        ];
    }

    public function toJson():string
    {
        return str_replace("\/", "/", json_encode($this->root, JSON_PRETTY_PRINT));
    }
}
