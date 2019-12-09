<?php
/**
 * HCORE CLI
 * @author carmine.rumma@healthwareinternational.com
 * @package hcore/cli
 */

namespace hcore\cli;


class PrecommitManager
{
    /**
     * @var PrecommitManager
     */
    public static $instance;

    /**
     * @var string
     */
    private $precommit_mock;

    /**
     * @var string
     */
    private $precommit_path;

    /**
     * @var string
     */
    private $precommit_path_lock;

    /**
     * @var string
     */
    private $resource_path;

    /**
     * @param string $precommit_path
     * @return PrecommitManager|null
     */
    public static function getInstance(string $precommit_path) {
        if(!self::$instance) {
            self::$instance = new PrecommitManager();
            self::$instance->resource_path = dirname(dirname(__DIR__)) . "/resources";
        }
        self::$instance->precommit_path = $precommit_path;
        self::$instance->precommit_path_lock = $precommit_path . ".lock";
        self::$instance->precommit_mock = file_get_contents(self::$instance->resource_path . "/precommit");
        return self::$instance;
    }

    public function read() : string {
        return $this->precommit_mock;
    }

    /**
     * @return self
     */
    public function initialize() : self{
        $config = include(dirname(dirname(__DIR__)) . "/configs/apitests.php");

        $this->precommit_mock = str_replace(["{{COLLECTION}}", "{{ENVIRONMENT}}"],
                                            [$config["collection"], $config["environment"]],
                                            $this->precommit_mock);
        return $this;
    }

    public function save() : bool {
        $write_res = file_put_contents($this->precommit_path, $this->precommit_mock);
        if ($write_res === false){
            return false;
        }
        if (true == chmod($this->precommit_path, 0755)){
            file_put_contents($this->precommit_path_lock, time());
            return true;
        }
        return false;
    }
}
