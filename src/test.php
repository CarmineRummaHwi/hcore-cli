<?php
/**
 * Created by PhpStorm.
 * User: crumma
 * Date: 2019-11-14
 * Time: 12:58
 */

set_time_limit(0);
$cwd = empty($_SERVER['PWD']) ? getcwd() : $_SERVER['PWD'];
require_once (dirname(__FILE__) . "/autoload.php");

\hcore\cli\Utilities::checkComposerInstalled();
