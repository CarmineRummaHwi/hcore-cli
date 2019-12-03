<?php
use \hcore\cli\HCli;

$cwd = empty($_SERVER['PWD']) ? getcwd() : $_SERVER['PWD'];
require_once (dirname(__FILE__) . "/autoload.php");
require_once (dirname(__FILE__) . "/bootstrap.php");

HCli::getInstance()->call("apitests init");
