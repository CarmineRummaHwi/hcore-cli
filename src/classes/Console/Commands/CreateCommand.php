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
        $log = new CLIColors();
        //print_r ($this->options);

        if (!isset($this->argv[2])){
            echo "Nome del progetto non specificato.\n";
            die;
        }

        if (false == \hcore\cli\Utilities::checkComposerInstalled()){
            echo $log->getColoredString("Composer is not installed".PHP_EOL.PHP_EOL, 'red');
            echo $log->getColoredString("Run this commands to install it:".PHP_EOL);
            echo $log->getColoredString(' curl -sS https://getcomposer.org/installer | php'.PHP_EOL.
                ' php composer.phar install'.PHP_EOL, 'red');
            echo $log->getColoredString("\n", 'black');
            die;
        }

        //echo "<pre>";print_r($this->argv);


        $bitbucketUser = "";
        $bitbucketPassword = "";

        $indexUser = array_search("-u", $this->argv);
        if ($indexUser !== false){
            $bitbucketUser = trim($this->argv[$indexUser + 1]);
        }
        $indexUser = array_search("-user", $this->argv);
        if ($indexUser !== false){
            $bitbucketUser = trim($this->argv[$indexUser + 1]);
        }
        $indexPwd = array_search("-p", $this->argv);
        if ($indexPwd !== false){
            $bitbucketPassword = trim($this->argv[$indexPwd + 1]);
        }
        $indexPwd = array_search("-password", $this->argv);
        if ($indexPwd !== false){
            $bitbucketPassword = trim($this->argv[$indexPwd + 1]);
        }

        if ($bitbucketUser == "" || $bitbucketPassword == ""){
            echo "Dati Bitbucket obbligatori.\n";
            die;
        }


        if ($this->argv[2]){
            $cwd = $this->getCWD();

            $authorItem = array(
                'name' => 'Vincenzo Romano',
                'email' => 'vincenzo.romano@healthtouch.eu',
            );

            $composer = new \hcore\cli\ComposerFactory();
            $composer->add("name", $this->argv[2]);
            $composer->add("type", "library");
            $composer->add("version", "1.0");
            $composer->add("description", "hcore installer");
            $composer->addAuthor($authorItem);
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

            echo $log->getColoredString("composer.json created!", 'green');
            echo $log->getColoredString("\n", 'black');


            $output = array();
            echo shell_exec("composer.phar install");
        }

    }

}
