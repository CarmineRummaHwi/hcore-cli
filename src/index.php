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
    echo $log->getColoredString("Usage:\n", "yellow");
    echo "  command [options] [arguments]\n\n";
    printAvailableCommands();
}

if ($command) {

    if ($command == "-h" || $command == "help") {
        printAvailableCommands();
    }

    if (file_exists(  dirname(__FILE__) . "/classes/Console/Commands/" . ucfirst($command) . "Command.php") && $command != "-h") {
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
    echo $log->getColoredString("Available command:\n", "yellow");

    echo $log->getColoredString("  hcore migrate\t", "green");
    echo "Migrate all Models\n";

    echo $log->getColoredString("  hcore cache:clean\t", "green");
    echo "Clear Autoloader Cache\n";

    echo $log->getColoredString("  hcore view:build\t", "green");
    echo "View Build Dist";
    echo $log->getColoredString("\n", "black");
}
