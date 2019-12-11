<?php
/**
 * HCORE CLI
 * @author carmine.rumma@healthwareinternational.com
 * @package hcore/cli
 */

require_once dirname(dirname(dirname(__FILE__))) . "/bootstrap.php";
class BaseCommand {

    const VERSION = '1.0';
    const COMMAND = 'hcore';
    public $name;
    public $options;
    public $argv;
    public $description;
    public $arguments;
    public $options_desc;
    public $usage = [];

    public function __construct($option = array())
    {
        $this->options = ($option);
    }

    public function getCWD():string {
        return getcwd();
    }

    public function exec():void {

    }

    public function preExec():void{
        //ddd($this->argv);
        if (is_cli()){
            /* @todo centralize command retrieve */
            $command = "";
            $opt = array();
            if (isset($this->argv[1])){
                $command = $this->argv[1];
                $opt = explode(":", $command);
            }

            if (!empty($opt[1])) {
                $command = $opt[0];
            }
            /**/
            if ($command != "create" && $command != "" && !file_exists($this->getCWD() . "/hcore.lock")){
                console()->displayError("Please launch the create command before any other command")->nl();
                die();
            }
        }
    }
    public function postExec():void{

    }

    public function getUsage():string {
        $line = "";
        if (sizeof($this->usage) > 0){
            foreach($this->usage as $usecase) {
                console()->display(self::COMMAND . " " . $this->name . " " . $usecase["action"], "green")
                         ->d("\t")
                         ->d($usecase["description"])
                         ->nl();
            }
        } else {
            $line = "  " . $this->name . " " . ((sizeof($this->options_desc) > 0) ? '[options]' : ' ') . ' ' . ((sizeof($this->arguments) > 0) ? '[<arguments>]' : ' ');
        }
        return $line;
    }

    public function checkHelp():bool {
        if (isset($this->argv[2])) {
            if ($this->argv[2] == "-h" || $this->argv[2] == "--help" || $this->argv[2] == "help") {
                return true;
            }
            return false;
        }
        return false;
    }

    public function showHelp():void {

        if (!empty($this->description)) {
            console()->d("Description:", "yellow")
                ->nl()
                ->space(2)
                ->d($this->description)
                ->nl(2);
        }

        console()->d("Usage:", "yellow")
            ->nl();

        $this->getUsage();
        console()->nl();


        console()->d("Arguments:", "yellow")
            ->nl();

        foreach ($this->arguments as $item) {

            console()->space(2)
                ->d($item["name"], "green")
                ->d("\t")
                ->d($item["description"])->nl();

        }
        console()->nl();

        if (sizeof($this->options_desc) > 0) {
            console()->d("Options:\n", "yellow");
            foreach ($this->options_desc as $item) {

                console()->space(2)
                    ->d($item["short"] . ",", "green")
                    ->d(" " . $item["regular"], "green")
                    ->d("\t")
                    ->d($item["description"])
                    ->nl();

            }
            console()->nl();
        }
    }

}
