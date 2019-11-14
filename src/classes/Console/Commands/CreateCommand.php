<?php



class CreateCommand extends BaseCommand {

    public $name        = "create";
    public $description = "HCore install - hcore create <name>";
    public $arguments   = [
        [
            "name"          => "name",
            "description"   => "project name"
        ],
    ];
    public $options_desc = [
        [
            "short"         => "-u",
            "regular"       => "--user",
            "description"   => "bitbucket username",
        ],
        [
            "short"         => "-p",
            "regular"       => "--password",
            "description"   => "bitbucket password",
        ]
    ];

    public function exec()
    {
        if (!isset($this->argv[2])){
            console()->displayError("Project name is required");
            die;
        }

        if (false == \hcore\cli\Utilities::checkComposerInstalled()){
            console()->displayError("Composer is not installed")
                     ->space(2)->d("Run this commands to install it:" . PHP_EOL)
                     ->space(2)->d('curl -sS https://getcomposer.org/installer | php' . PHP_EOL, 'red')
                     ->space(2)->d('php composer.phar install' . PHP_EOL, 'red')
                     ->nl();
            die;
        }

        $bitbucketUser = search_argv_val("-u", $this->argv);
        $bitbucketUser = empty($bitbucketUser) ? search_argv_val("--user", $this->argv) : $bitbucketUser;

        $bitbucketPassword = search_argv_val("-p", $this->argv);
        $bitbucketPassword = empty($bitbucketUser) ? search_argv_val("--password", $this->argv) : $bitbucketPassword;

        if (!$bitbucketUser || !$bitbucketPassword){
            console()->displayError("BitBucket Access is required");
            die;
        }

        if ($this->argv[2]){
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
            $composer->add("description", "hcore installer");
            //$composer->addAuthor($authorItem[0]);
            //$composer->addAuthor($authorItem[1]);
            $composer->add("minimum-stability", "dev");
            $composer->addRequire('hcore/core', '^0.3');
            $composer->addRequire('hcore/auth', '^0.3');
            $composer->addRequire('hcore/api', '^0.3');
            $composer->addRequire('hcore/notifier', '^0.3');
            $composer->addRequire('hcore/installer', '^0.3');
            $composer->addRequire('hcore/orm', '^0.3');
            $composer->addRequire('hcore/dmr', '^0.3');
            $composer->addRepository(array (
                'type' => 'git',
                'url' => "https://{$bitbucketUser}:{$bitbucketPassword}@bitbucket.org/HealthwareGroup/hcore.git",
            ));
            $composer->addRepository(array (
                'type' => 'git',
                'url' => "https://{$bitbucketUser}:{$bitbucketPassword}@bitbucket.org/HealthwareGroup/hcore.auth.git",
            ));
            $composer->addRepository(array (
                'type' => 'git',
                'url' => "https://{$bitbucketUser}:{$bitbucketPassword}@bitbucket.org/HealthwareGroup/hcore.api.git",
            ));
            $composer->addRepository(array (
                'type' => 'git',
                'url' => "https://{$bitbucketUser}:{$bitbucketPassword}@bitbucket.org/HealthwareGroup/hcore.notifier.git",
            ));
            $composer->addRepository(array (
                'type' => 'git',
                'url' => "https://{$bitbucketUser}:{$bitbucketPassword}@bitbucket.org/HealthwareGroup/hcore.installer.git",
            ));
            $composer->addRepository(array (
                'type' => 'git',
                'url' => "https://{$bitbucketUser}:{$bitbucketPassword}@bitbucket.org/HealthwareGroup/hcore.orm.git",
            ));
            $composer->addRepository(array (
                'type' => 'git',
                'url' => "https://{$bitbucketUser}:{$bitbucketPassword}@bitbucket.org/HealthwareGroup/hcore.dmr.git",
            ));
            $composer->addRepository(array (
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

            console()->displaySuccess("composer.json created!");

            $output = array();
            echo shell_exec("composer install");
        }

    }

}
