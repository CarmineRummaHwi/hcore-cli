<?php



class HtaccessCommand extends BaseCommand {

    public $name        = "htaccess";
    public $description = "HCore htaccess - .htaccess manager";
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
       // ppp($this->options);
       // ddd($this->argv);

        $cwd = $this->getCWD();
        /** @var \hcore\cli\HtaccessManager */
        $htaccessManager = \hcore\cli\HtaccessManager::getInstance($cwd . DIRECTORY_SEPARATOR . ".htaccess");

        if ($this->options[1] == "redirect-http"){
            $action  = $this->argv[2]; // enable || disable
            if ($action == "enable"){
                $htaccessManager->enableHttpRedirect()
                                ->save();
                console()->displaySuccess("Http Redirect enabled");

            } elseif ($action == "disable"){
                $htaccessManager->disableHttpRedirect()
                                ->save();
                console()->displaySuccess("Http Redirect disabled");

            } else {
                console()->displayError("Invalid action spec.");


            }
        }

        if ($this->options[1] == "redirect-https"){
            $action  = $this->argv[2]; // enable || disable
            if ($action == "enable"){
                $htaccessManager->enableHttpsRedirect()
                                ->save();
                console()->displaySuccess("Https Redirect enabled");

            } elseif ($action == "disable"){
                $htaccessManager->disableHttpsRedirect()
                                ->save();
                console()->displaySuccess("Https Redirect disabled");

            } else {
                console()->displayError("Invalid action spec.");
            }
        }
    }

}
