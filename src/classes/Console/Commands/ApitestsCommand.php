<?php
/**
 * HCORE CLI
 * @author carmine.rumma@healthwareinternational.com
 * @package hcore/cli
 */


class ApitestsCommand extends BaseCommand {

    public $name        = "apitests";
    public $description = "HCore API massive tests <name>";
    public $arguments   = [

    ];
    public $options_desc = [

    ];
    public $usage = [
        [
            "action" => "init",
            "description" => "Initialize the pre-commit Git hook"
        ],
        [
            "action" => "run",
            "description" => "Run the API Tests and save to apireport.log"
        ],
        [
            "action" => "run -c",
            "description" => "Run the API Tests and show report to console"
        ]
    ];

    public function exec() :void
    {
        if (false == \hcore\cli\Utilities::checkNodeJsInstalled()){
            console()->displayError("nodejs is not installed")
                     ->space(2)->d("Go to the site https://nodejs.org/en/download/ and download the necessary binary files." . PHP_EOL)
                     ->nl();
            die;
        }

        if (false == \hcore\cli\Utilities::checkNPMInstalled()){
            console()->displayError("npm is not installed")
                     ->space(2)->d("Run this commands to install it: npm install npm -g" . PHP_EOL)
                     ->nl();
            die;
        }

        if (false == \hcore\cli\Utilities::checkNewmanInstalled()){
            console()->displayError("newman is not installed")
                     ->space(2)->d("Run this commands to install it: npm i newman -g" . PHP_EOL)
                     ->nl();
            die;
        }

        $cwd = $this->getCWD();
        $pc_path = $cwd . DIRECTORY_SEPARATOR . ".git" . DIRECTORY_SEPARATOR . "hooks" . DIRECTORY_SEPARATOR . "pre-commit";
        $precommitManager = \hcore\cli\PrecommitManager::getInstance($pc_path);

        $action  = $this->argv[2]; // init || run
        if ($action == "init"){
            console()->d("precommit initialization...")
                     ->nl();

            if (true === $precommitManager->initialize()->save()){
                console()->displaySuccess("git pre-commit hook correctly initializated.");
            } else {
                console()->displayError("Error on write git pre-commit hook.")
                         ->nl()
                         ->space(2)->d("Please give write permissions to Project Directory");
            }

        } elseif ($action == "run"){
            console()->d("API Test running..")
                     ->nl();

            $api_tests_config = include(dirname(dirname(dirname(__DIR__))) . "/configs/apitests.php");
            $collection = $cwd . $api_tests_config["collection"];
            $environment = $cwd . $api_tests_config["environment"];

            if (isset($this->argv[3]) && $this->argv[3] == "-c"){
                passthru("newman run {$collection} -e {$environment} ");
            } else {
                $report_abspath = $cwd . "/tests/api/apireport.log";
                if (file_exists($report_abspath)){
                    unlink($report_abspath);
                }
                shell_exec("newman run {$collection} -e {$environment} -k > " . $report_abspath);
                console()->displaySuccess("report successfully generated at /tests/api/apireport.log");
            }

        } else {
            console()->displayError("Invalid action spec.");
        }


    }

}
