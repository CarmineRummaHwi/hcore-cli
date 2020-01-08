<?php
/**
 * HCORE CLI
 * @author carmine.rumma@healthwareinternational.com
 * @package hcore/cli
 */

class ModuleCommand extends BaseCommand
{
    public $name        = "module";
    public $description = "HCore Module command";
    public $arguments   = [
        [
            "name"          => "module",
            "description"   => "module name to add/remove"
        ],
    ];
    public $usage = [
        /*[
            "action"        => "add",
            "separator"     => ":",
            "description"   => "Add module dependency and refresh the structure",
            "arguments"     => array(
                "path" => "module"
            )
        ],*/
        [
            "action"        => "remove",
            "separator"     => ":",
            "description"   => "Remove module dependency from composer and refresh the structure",
            "arguments"     => array(
                "path" => "module"
            )
        ]
    ];


    public function exec() :void
    {
        if (!isset($this->options[1])) {
            console()->displayError("no action spec.");
            die;
        }

        $cwd = $this->getCWD();
        if ($this->options[1] == "remove") {

            if (!isset($this->argv[2])) {
                console()->displayError("module name is required");
                die;
            }

            //ddd($this->argv);
            $module = $this->argv[2];
            shell_exec("composer remove hcore/$module");
        } /*else if ($this->options[1] == "add") {

            if (!isset($this->argv[2])) {
                console()->displayError("module name is required");
                die;
            }

            //ddd($this->argv);
            $module = $this->argv[2];
            shell_exec("composer require $module");

        }  */else {
            console()->d("Invalid action ", "red")
                     ->d($this->options[1], "dark_gray")
                     ->space()->displayError("spec.");
        }
    }
}
