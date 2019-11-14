<?php
/**
 * Created by PhpStorm.
 * User: crumma
 * Date: 08/10/18
 * Time: 16:35
 */

class BaseCommand {

    public $name;
    public $options;
    public $argv;
    public $description;
    public $arguments;
    public $options_desc;

    public function __construct($option = array())
    {
        $this->options = ($option);
    }

    public function getCWD(){
        return getcwd();
    }

    public function exec(){

    }

    public function getUsage(){
        $line = "  " . $this->name . " " . ((sizeof($this->options_desc) > 0) ? '[options]' : ' ') . ' ' . ((sizeof($this->arguments) > 0) ? '[<arguments>]' : ' ');
        return $line;
    }

    public function checkHelp(){
       // print_r($this->argv[2]);die;
        if (isset($this->argv[2])) {
            if ($this->argv[2] == "-h" || $this->argv[2] == "--help") {

                $log = new CLIColors();

                if (!empty($this->description)) {
                    echo $log->getColoredString("Description:\n", "yellow");
                    echo $log->getColoredString("  " . $this->description . "\n\n");
                }

                echo $log->getColoredString("Usage:\n", "yellow");
                echo $log->getColoredString($this->getUsage() . "\n\n");


                echo $log->getColoredString("Arguments:\n", "yellow");
                foreach ($this->arguments as $item) {

                    echo $log->getColoredString("  " . $item["name"] . "\t", "green");
                    echo $item["description"] . "\n";

                }
                echo "\n";


                echo $log->getColoredString("Options:\n", "yellow");
                foreach ($this->options_desc as $item) {

                    echo $log->getColoredString("  " . $item["short"] . ",", "green");
                    echo $log->getColoredString(" " . $item["regular"] . "\t", "green");
                    echo $item["description"] . "\n";

                }
                echo "\n";
                die();
            }

        }
    }

}
