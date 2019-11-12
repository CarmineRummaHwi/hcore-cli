<?php

class CacheCommand extends BaseCommand {

    public function exec()
    {
        $log = new CLIColors();


        if ($this->options[1] == "clean") {
            echo $log->getColoredString("Starting autoload clean..", 'light_gray');
            echo "\n";



            echo $log->getColoredString("Autoloader Cache clean!\n", 'green', 'black');
        } else {
            echo "No option specified";
        }

    }

}
