#!/usr/bin/env php
<?php
set_time_limit(0);

$cwd = empty($_SERVER['PWD']) ? getcwd() : $_SERVER['PWD'];
require_once (dirname(__FILE__) . "/autoload.php");
//dd($argv);
$command = $argv[1];

$opt = explode(":", $command);

$log = new CLIColors();

if (!empty($opt[1])) {
    $command = $opt[0];
}else{
    //echo $log->getColoredString("Usage:\n", "yellow");
    //echo "  command [options] [arguments]\n\n";
    //printAvailableCommands();
}

if ($command) {

    if ($command == "-h" || $command == "help") {
        printAvailableCommands();
        die();
    }

    if (file_exists(dirname(__FILE__) . "/classes/Console/Commands/" . ucfirst($command) . "Command.php")) {
        require dirname(__FILE__)  . "/classes/Console/Commands/" . ucfirst($command) . "Command.php";
        $commandClass = ucfirst($command) . "Command";
        /** @var BaseCommand $command */
        $command = new $commandClass($opt);
        $command->argv = $argv;
        print $command->exec();
    } else {
        echo $log->getColoredString("HCORE :: Command not Found", "red");
        echo $log->getColoredString("\n", "black");
    }
}

function printAvailableCommands() {
    $log = new CLIColors();
    echo $log->getColoredString("Usage:\n", "yellow");
    echo "  command [options] [arguments]\n\n";

    echo $log->getColoredString("Options:\n", "yellow");
    echo $log->getColoredString("  -h, --help", "green");
    echo "\tDisplay this Help message\n\n";

    echo $log->getColoredString("Available Commands:\n", "yellow");
    $res = scandir(dirname(__FILE__) . "/classes/Console/Commands/");
    $commands = [];
    foreach($res as $item){
        if ($item !== '.' && $item !== '..'){
            if (file_exists(  dirname(__FILE__) . "/classes/Console/Commands/" . $item)) {
                require_once dirname(__FILE__) . "/classes/Console/Commands/" . $item;
                $commandClass = pathinfo($item, PATHINFO_FILENAME);
                /** @var BaseCommand $command */
                $command = new $commandClass();
                //print_r($command);
                //$command->getCommandHelp();

                echo $log->getColoredString("  " . $command->name , "green");
                echo $log->getColoredString("\t" . $command->description . "\n");

            } else {

            }
        }
    }
}

