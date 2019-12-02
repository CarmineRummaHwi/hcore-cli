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
        if (!isset($this->options[1])){
            console()->displayError("Project name is required");
            die;
        }

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

        if ($this->options[1] == "banlist"){
            $add = search_argv_val("-a", $this->argv);
            $add = empty($add) ? search_argv_val("--add", $this->argv) : $add;

            $remove = search_argv_val("-r", $this->argv);
            $remove = empty($remove) ? search_argv_val("--remove", $this->argv) : $remove;

            if (!empty($add)) {
                $htaccessManager->addToBanlist($add)
                                ->save();
                console()->displaySuccess($add . " added to the ban list");
                die();
            }else{
                if(empty($remove)) {
                    console()->displayError("ban list item missing");
                    die();
                }
            }

            if (!empty($remove)) {
                $htaccessManager->removeFromBanlist($remove)
                                ->save();
                console()->displaySuccess($remove . " removed from the ban list");
                die();
            }else{
                console()->displayError("ban list item missing");
                die();
            }

        }

        if ($this->options[1] == "security-cleaner"){
            $action  = $this->argv[2]; // enable || disable
            if ($action == "enable"){
                $htaccessManager->enableSecurityCleaner()
                    ->save();
                console()->displaySuccess("Security cleaner enabled");
            } elseif ($action == "disable"){
                $htaccessManager->disableSecurityCleaner()
                    ->save();
                console()->displaySuccess("Security cleaner disabled");
            } else {
                console()->displayError("Invalid action spec.");
            }
        }

        if ($this->options[1] == "cache"){
            $action  = $this->argv[2]; // enable || disable
            if ($action == "enable"){
                $htaccessManager->enableHtaccessCache()
                    ->save();
                console()->displaySuccess("Security cleaner enabled");
            } elseif ($action == "disable"){
                $htaccessManager->disableHtaccessCache()
                    ->save();
                console()->displaySuccess("Security cleaner disabled");
            } else {
                console()->displayError("Invalid action spec.");
            }
        }

        if ($this->options[1] == "security"){
            $action  = $this->argv[2]; // enable || disable
            if ($action == "enable"){
                $htaccessManager->enableHtaccessSecurity()
                    ->save();
                console()->displaySuccess("Security enabled");
            } elseif ($action == "disable"){
                $htaccessManager->disableHtaccessSecurity()
                    ->save();
                console()->displaySuccess("Security disabled");
            } else {
                console()->displayError("Invalid action spec.");
            }
        }

        if ($this->options[1] == "security-advanced"){
            $action  = $this->argv[2]; // enable || disable
            if ($action == "enable"){
                $htaccessManager->enableHtaccessSecurityAdvanced()
                                ->save();
                console()->displaySuccess("Security advanced enabled");
            } elseif ($action == "disable"){
                $htaccessManager->disableHtaccessSecurityAdvanced()
                                ->save();
                console()->displaySuccess("Security advanced disabled");
            } else {
                console()->displayError("Invalid action spec.");
            }
        }

        if ($this->options[1] == "cors"){
            $add = search_argv_val("-a", $this->argv);
            $add = empty($add) ? search_argv_val("--add", $this->argv) : $add;

            $remove = search_argv_val("-r", $this->argv);
            $remove = empty($remove) ? search_argv_val("--remove", $this->argv) : $remove;

            if (!empty($add)) {
                $htaccessManager->addCorsRule($add)
                                ->save();
                console()->displaySuccess($add . " added to cors policy");
                die();
            }else{
                if(empty($remove)) {
                    console()->displayError("ban list item missing");
                    die();
                }
            }

            if (!empty($remove)) {
                $htaccessManager->removeCorsRule($remove)
                                ->save();
                console()->displaySuccess($remove . " removed from cors policy");
                die();
            }else{
                console()->displayError("ban list item missing");
                die();
            }

        }

        if ($this->options[1] == "xframe"){
            $add = search_argv_val("-a", $this->argv);
            $add = empty($add) ? search_argv_val("--add", $this->argv) : $add;

            $remove = search_argv_val("-r", $this->argv);
            $remove = empty($remove) ? search_argv_val("--remove", $this->argv) : $remove;

            if (!empty($add)) {
                $htaccessManager->addXFrameOptions($add)
                    ->save();
                console()->displaySuccess($add . " added to x-frame options policy");
                die();
            }else{
                if(empty($remove)) {
                    console()->displayError("ban list item missing");
                    die();
                }
            }

            if (!empty($remove)) {
                $htaccessManager->removeXFrameOptions($remove)
                    ->save();
                console()->displaySuccess($remove . " removed from x-frame options policy");
                die();
            }else{
                console()->displayError("ban list item missing");
                die();
            }

        }

        if ($this->options[1] == "security-policy"){
            $action  = $this->argv[2]; // enable || disable
            if ($action == "enable"){
                $htaccessManager->enableSecurityPolicy()
                    ->save();
                console()->displaySuccess("Security policy enabled");
            } elseif ($action == "disable"){
                $htaccessManager->disableSecurityPolicy()
                    ->save();
                console()->displaySuccess("Security policy disabled");
            } else {
                console()->displayError("Invalid action spec.");
            }
        }
    }

}
