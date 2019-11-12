<?php



class CreateCommand extends BaseCommand {

    public $name        = "create";
    public $description = "Hcore install - hcore create <name>";
    public $arguments   = [
        [
            "name"          => "name",
            "description"   => "project name",
        ],
    ];
    public $options_desc = [
        [
            "short"         => "-u",
            "regular"       => "-user",
            "description"   => "bitbucket username",
        ],
        [
            "short"         => "-p",
            "regular"       => "-password",
            "description"   => "bitbucket password",
        ]
    ];
    public function exec()
    {
        $log = new CLIColors();
        //print_r ($this->options);

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
            $composer->addRequire('hcore/core', '0.3.4');
            $composer->addRepository(array (
                'type' => 'git',
                'url' => 'https://bitbucket.org/HealthwareGroup/hcore.git',
            ));
            $composer->addRepository(array (
                'type' => 'git',
                'url' => 'https://bitbucket.org/cmsff/libs.git',
            ));
            $composer->addAutoload("psr-4", array (
                'hcore\\' => 'src/',
                'hcore\\app\\conf\\' => "app/conf/"
            ));
            $composer->addAutoload("classmap", array (
                'src/',
            ));

            $fp = fopen($cwd . '/composer.json', 'w');
            fwrite($fp, $composer->toJson());
            fclose($fp);

            echo $log->getColoredString("composer.json created!", 'green');
            echo $log->getColoredString("\n", 'black');

        }

    }

}
