<?php
/**
 * Created by PhpStorm.
 * User: crumma
 * Date: 08/10/18
 * Time: 16:35
 */

class BaseCommand {

    public $options;
    public $argv;
    public $description;

    public function __construct($option = array())
    {
        $this->options = ($option);
    }

    public function getCWD(){
        return getcwd();
    }

    public function exec(){

    }

}
