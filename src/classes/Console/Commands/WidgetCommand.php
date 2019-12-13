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

    public function exec() :void
    {
        if (!isset($this->options[1])) {
            console()->displayError("Project name is required");
            die;
        }

        $cwd = $this->getCWD();
        if ($this->options[1] == "create") {
            ddd($this->argv);
        }
    }
}
