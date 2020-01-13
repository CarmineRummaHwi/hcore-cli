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
    public $options_desc = [
        [
            "short"         => "-d",
            "regular"       => "--dev",
            "description"   => "install also the dev requirements",
        ],
        [
            "short"         => "-j",
            "regular"       => "--json",
            "description"   => "only generate composer.json without install",
        ]
    ];
    public $usage = [
        [
            "action"        => "add",
            "separator"     => ":",
            "description"   => "Add module dependency and refresh the structure",
            "arguments"     => array(
                "path" => "module"
            )
        ],
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
        } else if ($this->options[1] == "add") {

            if (!isset($this->argv[2])) {
                console()->displayError("module name is required");
                die;
            }

            $module = $this->argv[2];
            $composer = new \hcore\cli\ComposerFactory();
            $composer->read($this->getCWD() . "/composer.json");
            //$composer->dump();
            $moduleDef = \hcore\cli\HCli::getModuleDef($module);
            if ($moduleDef){

                if ($composer->checkRequireDep($moduleDef["dep"])){
                    console()->d("module hcore/", "light_blue")
                             ->d($module, "light_blue")
                             ->d(" is already present in this App.", "light_blue")
                             ->nl();
                    die;
                }

                $composer->addRequire($moduleDef["dep"], $moduleDef["version"]);
                $composer->addRepository($moduleDef["repository"]);
                $fp = fopen($cwd . '/composer.json', 'w');
                if ( !$fp ) {
                    console()->displayError("can't write the file composer.json. Please check the folder permissions!");
                    die;
                }
                fwrite($fp, $composer->toJson());
                fclose($fp);

                console()->d("module dependency hcore/", "green")
                         ->d($module, "light_green")
                         ->d(" added successfully!", "green")
                         ->nl();

                $requireDev = false;
                $composerCommandOption = "--no-dev";
                $install = true;
                if ($this->checkOption("d", "dev")){
                    $requireDev = true;
                    $composerCommandOption = "";
                    /*$this->checkOption("d", "dev", function ($value){
                        die("Value enetered: " . $value);
                    });*/
                }
                if ($this->checkOption("j", "json")){
                    $install = false;
                }
                if ($install == true) {
                    echo shell_exec("composer install " . $composerCommandOption);
                }

            } else {
                console()->d("module hcore/", "light_red")
                         ->d($module, "light_red")
                         ->d(" not exists!", "light_red")
                         ->nl();
                die;
            }
            //shell_exec("composer require $module");

        } else {
            console()->d("Invalid action ", "red")
                     ->d($this->options[1], "dark_gray")
                     ->space()->displayError("spec.");
        }
    }
}
