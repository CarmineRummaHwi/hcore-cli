<?php
/**
 * HCORE CLI
 * @author carmine.rumma@healthwareinternational.com
 * @package hcore/cli
 */

class WidgetCommand extends BaseCommand
{
    public $name        = "widget";
    public $description = "HCore Widget command";
    public $arguments   = [
        [
            "name"          => "module/widget",
            "description"   => "widget path. if only the module is specified all the widgets will be overwritten"
        ],
    ];
    public $usage = [
        [
            "action"        => "create",
            "separator"     => ":",
            "description"   => "Override Hcore module widget",
            "arguments"     => array(
                "path" => "module/widget"
            )
        ]
    ];

    private function getWidgetPath(string $module_name = "", string $widget_name = ""):string{
        return $this->getCWD() . "/vendor/hcore/" . $module_name . "/assets/widgets/" . $widget_name;
    }

    private function overrideFolder(string $subPath = ""):string {
        return $this->getCWD() . "/app/views/widgets" . $subPath;
    }

    private function createIfNotExists(string $path = ""): void{
        if (!file_exists($this->overrideFolder($path))){
            \hcore\cli\Utilities::createDir($this->overrideFolder(), 0755);
        }
    }

    private function copyOverrideFolder(string $module = "", string $widget = ""): bool {

        $this->createIfNotExists();

        $module_path = $this->getCWD() . "/vendor/hcore/" . $module;
        if (!file_exists($module_path)){
            console()->d("Module", "red")
                     ->space(1)
                     ->d($module, "dark_gray")
                     ->space(1)->d("not found!", "red")
                     ->nl();

            return false;
        }
        $this->createIfNotExists(DIRECTORY_SEPARATOR . $module);
        if ($widget != "") {
            $widget_path = $this->getWidgetPath($module, $widget);
            if (!file_exists($widget_path)){

                console()->d("Widget", "red")
                         ->space(1)
                         ->d($widget, "dark_gray")
                         ->space(1)->d("not found!", "red");

                return false;
            }
            //$this->createIfNotExists(DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $widget);
            /*echo ($widget_path);
            echo ($this->overrideFolder(DIRECTORY_SEPARATOR . $widget));
            die;*/
            \hcore\cli\Utilities::copyDirectory($widget_path, $this->overrideFolder(DIRECTORY_SEPARATOR . $widget));

            return true;
        } else {
            $widget_path = $this->getWidgetPath($module);
            $directories = glob($widget_path . '*' , GLOB_ONLYDIR);
            foreach($directories as $item){
                if (!file_exists($item)){

                    console()->d("Widget", "red")
                        ->space(1)
                        ->d($widget, "dark_gray")
                        ->space(1)->d("not found!", "red");

                    return false;
                }

                \hcore\cli\Utilities::copyDirectory($item, $this->overrideFolder(DIRECTORY_SEPARATOR . basename($item)));
            }
            return true;
        }

    }

    public function exec() :void
    {
        $cwd = $this->getCWD();
        if ($this->options[1] == "create") {

            if (!isset($this->argv[2])) {
                console()->displayError("No module/widget spec!");
                die;
            }

            //ddd($this->argv);
            $mod_widget_path = $this->argv[2];
            $parsed = explode("/", $mod_widget_path);
            if (sizeof($parsed) > 0){
                $module = $parsed[0];
                if (!empty($module) && isset($parsed[1])){
                    $widget = $parsed[1];
                    $this->copyOverrideFolder($module, $widget);
                    console()->displaySuccess("Widget $widget published in app/views/widgets/$widget");
                } else if(!empty($module)){
                    $this->copyOverrideFolder($module);
                    console()->displaySuccess("All $module widgets published in app/views/widgets/");
                }
            }
        }
    }
}
