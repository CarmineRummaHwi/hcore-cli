<?php
/**
 * HCORE CLI
 * @author carmine.rumma@healthwareinternational.com
 * @package hcore/cli
 */

use \hcore\cli\HCli;

class CreateCommand extends BaseCommand
{
    public $name        = "create";
    public $description = "Create a new HCore application";
    public $arguments   = [
        [
            "name"          => "name",
            "description"   => "your project name"
        ],
    ];
    public $options_desc = [
        /*[
            "short"         => "-u",
            "regular"       => "--user",
            "description"   => "bitbucket username",
        ],
        [
            "short"         => "-p",
            "regular"       => "--password",
            "description"   => "bitbucket password",
        ]*/
    ];
    public $usage = [
        [
            "action" => "<name>",
            "description" => "Create HCore Project"
        ]
    ];

    public function exec():void
    {
        if (!isset($this->argv[2])) {
            console()->displayError("Project name is required");
            console()->space(2)->d("hcore create <project_name>", "green")->nl(2);
            die;
        }

        if (false == \hcore\cli\Utilities::checkComposerInstalled()) {
            console()->displayError("Composer is not installed")
                     ->space(2)->d("Run this commands to install it:" . PHP_EOL)
                     ->space(2)->d('curl -sS https://getcomposer.org/installer | php' . PHP_EOL, 'red')
                     ->space(2)->d('php composer.phar install' . PHP_EOL, 'red')
                     ->nl();
            die;
        }

        $prefix = "";
        /*
        $bitbucketUser = search_argv_val("-u", $this->argv);
        $bitbucketUser = empty($bitbucketUser) ? search_argv_val("--user", $this->argv) : $bitbucketUser;

        $bitbucketPassword = search_argv_val("-p", $this->argv);
        $bitbucketPassword = empty($bitbucketUser) ? search_argv_val("--password", $this->argv) : $bitbucketPassword;

        if (!$bitbucketUser || !$bitbucketPassword){
            console()->displayError("BitBucket Access is required");
            die;
        }
        $prefix = $bitbucketUser . ":" . $bitbucketPassword . "@";
        */

        if ($this->argv[2]) {
            $cwd = $this->getCWD();
            $authorItems     = array();
            $authorItems[]   = array(
                'name'  => 'Carmine Rumma',
                'email' => 'carmine.rumma@healthwareinternational.com',
            );
            $authorItems[]   = array(
                'name'  => 'Vincenzo Romano',
                'email' => 'vincenzo.romano@healthwareinternational.com',
            );

            $composer = new \hcore\cli\ComposerFactory();
            $composer->add("name", "hcore/" . $this->argv[2]);
            $composer->add("type", "library");
            $composer->add("version", "1.0");
            $composer->add("description", "A new HCore application");
            //$composer->addAuthor($authorItem[0]);
            //$composer->addAuthor($authorItem[1]);
            $composer->add("minimum-stability", "dev");
            $composer->addRequire('hcore/core', '^0.3');
            $composer->addRequire('hcore/auth', '^0.3');
            $composer->addRequire('hcore/api', '^0.3');
            $composer->addRequire('hcore/notifier', '^0.3');
            $composer->addRequire('hcore/installer', '^0.3');
            $composer->addRequire('hcore/orm', '^0.3');
            $composer->addRepository(array(
                'type' => 'git',
                'url' => "https://{$prefix}bitbucket.org/HealthwareGroup/hcore.git",
            ));
            $composer->addRepository(array(
                'type' => 'git',
                'url' => "https://{$prefix}bitbucket.org/HealthwareGroup/hcore.auth.git",
            ));
            $composer->addRepository(array(
                'type' => 'git',
                'url' => "https://{$prefix}bitbucket.org/HealthwareGroup/hcore.api.git",
            ));
            $composer->addRepository(array(
                'type' => 'git',
                'url' => "https://{$prefix}bitbucket.org/HealthwareGroup/hcore.notifier.git",
            ));
            $composer->addRepository(array(
                'type' => 'git',
                'url' => "https://{$prefix}bitbucket.org/HealthwareGroup/hcore.installer.git",
            ));
            $composer->addRepository(array(
                'type' => 'git',
                'url' => "https://{$prefix}bitbucket.org/HealthwareGroup/hcore.orm.git",
            ));
            /* DMR */
            console()->d("Do you need module DMR? [y/N]:");
            $handle = fopen("php://stdin", "r");
            $res    = fgets($handle);
            if (trim($res) === "" || trim($res) === "y") {
                $composer->addRequire('hcore/dmr', '^0.3');
                $composer->addRepository(array(
                    'type' => 'git',
                    'url' => "https://{$prefix}bitbucket.org/HealthwareGroup/hcore.dmr.git",
                ));
            }
            /* DMR */

            /* UPLOADER */
            console()->d("Do you need module UPLOADER? [y/N]:");
            $handle = fopen("php://stdin", "r");
            $res    = fgets($handle);
            if (trim($res) === "" || trim($res) === "y") {
                $composer->addRequire('hcore/uploader', '^0.3');
                $composer->addRepository(array(
                    'type' => 'git',
                    'url' => "https://{$prefix}bitbucket.org/HealthwareGroup/hcore.uploader.git",
                ));
            }
            /* UPLOADER */

            $composer->addRepository(array(
                'type' => 'git',
                'url' => 'https://bitbucket.org/cmsff/libs.git',
            ));
            /*
            $composer->addAutoload("psr-4", array (
                'hcore\\' => 'src/',
                'hcore\\app\\conf\\' => "app/conf/"
            ));
            $composer->addAutoload("classmap", array (
                'src/',
            ));
            */

            $composer->addScripts();

            $fp = fopen($cwd . '/composer.json', 'w');
            fwrite($fp, $composer->toJson());

            fclose($fp);

            $fp = fopen($cwd . '/hcore.lock', 'w');
            fwrite($fp, json_encode(array(
                "name" => "hcore/" . $this->argv[2]
            )));
            fclose($fp);

            console()->displaySuccess("composer.json created!");

            $this->askBitbucketConsumer();

            HCli::getInstance()->call("apitests init");

            echo shell_exec("composer install");
        }
    }

    private function askBitbucketConsumer(): void
    {
        if (!file_exists($this->getCWD() . '/auth.json')) {
            console()->d("Please goto:", "blue")
                     ->nl()
                     ->d("https://bitbucket.org/account/user/<yoususername>/api", "blue")
                     ->nl();
            console()->d("and add Consumer OAuth", "blue")->nl(2);
            console()->d("Please insert consumer-key:");

            $bitbucketData = array();

            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            $bitbucketData["consumer-key"] = trim($line);

            console()->d("Please insert consumer-secret:");

            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            $bitbucketData["consumer-secret"] = trim($line);

            $out = array("bitbucket-oauth" => array("bitbucket.org" => $bitbucketData));
            $fp = fopen($this->getCWD() . '/auth.json', 'w');
            fwrite($fp, json_encode($out, JSON_PRETTY_PRINT));
            fclose($fp);

            console()->displaySuccess("Bitbucket consumer saved!");
        }
    }
}
