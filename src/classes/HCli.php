<?php
/**
 * HCORE CLI
 * @author carmine.rumma@healthwareinternational.com
 * @package hcore/cli
 */

namespace hcore\cli;

require_once dirname(dirname(__FILE__)) . "/bootstrap.php";
class HCli
{
    /** @var HCli */
    private static $instance;

    const VERSION = '0.2.3';
    public $classes_path;
    private const name = "HCli";

    /**
     * @return HCli
     */
    public static function getInstance() : HCli
    {
        if (!self::$instance) {
            self::$instance = new HCli();
            self::$instance->classes_path = dirname(__FILE__);
        }
        return self::$instance;
    }


    /**
     * @param String $command
     * @param array|null $opt
     * @param array|null $argv
     * @param bool $trigger indicates whether the current command is triggered by the call method
     *
     * @return void
     */
    public function run(String $command = "", ?array $opt = array(), ?array $argv = array(), bool $trigger = false): void
    {
        if ($command) {
            if ($command == "-h" || $command == "--help") {
                self::printAvailableCommands();
                die();
            }

            if ($command == "-V" || $command == "--version") {
                self::getApplicationVersion();
                die();
            }

            if (file_exists($this->classes_path . "/Console/Commands/" . ucfirst($command) . "Command.php")) {
                require $this->classes_path . "/Console/Commands/" . ucfirst($command) . "Command.php";

                $commandClass = ucfirst($command) . "Command";

                /**
                 * @var \BaseCommand
                 */
                $CmdClass = new $commandClass($opt);
                $CmdClass->argv = $argv;
                $CmdClass->cli_options = Utilities::getOptions(["dev"]);
                if (true == $CmdClass->checkHelp()) {
                    $CmdClass->showHelp();
                    die();
                }
                if ($trigger == true){
                    $CmdClass->is_trigger = true;
                }
                $CmdClass->preExec();
                print $CmdClass->exec();
                $CmdClass->postExec();
            } else {
                console()->displayError(self::name . ": command not Found");
            }
        } else {
            // No command Spec
            self::printAvailableCommands();
            die();
        }
    }

    /**
     * @param String $command
     * @param array|null $opt
     * @param array|null $argv
     */
    public function call(String $command = "", ?array $opt = array(), ?array $argv = array()): void
    {
        $argv_from_command = explode(" ", $command);
        $_command = "";
        $_opt = array();

        if (count($argv_from_command) > 0) {
            array_unshift($argv_from_command, "hcore");
            if (isset($argv_from_command[1])) {
                $_command = $argv_from_command[1];
                $_opt = explode(":", $command);
            }

            if (!empty($opt[1])) {
                $_command = $opt[0];
            }
            self::getInstance()->run($_command, $_opt, $argv_from_command, true);
        }
    }

    /**
     *
     */
    public static function printAvailableCommands():void
    {
        console()->display("Usage:", "yellow")
                 ->nl()
                 ->space(2)
                 ->display("command [options] [arguments]");

        console()->nl(2);

        console()->display("Options:", "yellow")
                 ->nl()
                 ->space(2)
                 ->display("-h, --help", "green")
                 ->d("\tDisplay this Help message")
                 ->nl()
                 ->space(2)
                 ->display("-V, --version", "green")
                 ->d("\tDisplay this application version");

        console()->nl(2);

        console()->display("Available Commands:", "yellow")
                 ->nl();

        $res = scandir(dirname(__FILE__) . "/Console/Commands/");
        $commands = [];
        foreach ($res as $item) {
            if ($item !== '.' && $item !== '..') {
                if (file_exists(dirname(__FILE__) . "/Console/Commands/" . $item)) {
                    require_once dirname(__FILE__) . "/Console/Commands/" . $item;
                    $commandClass = pathinfo($item, PATHINFO_FILENAME);
                    $command = new \BaseCommand();
                    /** @var BaseCommand $command */
                    $command = new $commandClass();

                    console()->display("  " . $command->name, "green");
                    console()->display("\t" . $command->description . "\n");
                } else {
                }
            }
        }
    }

    public static function getApplicationVersion()
    {
        $ver = self::VERSION;
        /*
        $dir = dirname(dirname(__DIR__));
        exec("cd $dir; git describe --abbrev=0 --tags", $version_mini_hash);
        if (is_array($version_mini_hash)){
            $ver = $version_mini_hash[0];
            $clean_ver = explode("-", $ver);
            if (is_array($clean_ver)){
                $ver = $clean_ver[0];
            }
        }
        */
        console()->d(self::name)
                 ->space(1)
                 ->d($ver, "green")
                 ->nl();
    }
}
