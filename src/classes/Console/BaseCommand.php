<?php
/**
 * Created by PhpStorm.
 * User: crumma
 * Date: 08/10/18
 * Time: 16:35
 */

class BaseCommand {

    const VERSION = '1.0';
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

        if (isset($this->argv[2])) {
            if ($this->argv[2] == "-h" || $this->argv[2] == "--help") {

                if (!empty($this->description)) {
                    console()->d("Description:", "yellow")
                             ->nl()
                             ->space(2)
                             ->d($this->description)
                             ->nl(2);
                }

                console()->d("Usage:", "yellow")
                         ->nl()
                         ->d($this->getUsage() . "\n\n");


                console()->d("Arguments:", "yellow")
                         ->nl();

                foreach ($this->arguments as $item) {

                    console()->space(2)
                             ->d($item["name"], "green")
                             ->d("\t")
                             ->d($item["description"])->nl();

                }
                console()->nl();


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
                die();
            }

        }
    }

}
