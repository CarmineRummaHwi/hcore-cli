<?php
namespace hcore\cli\Tests;
use PHPUnit\Framework\TestCase;
use hcore\cli\HCli;
class TestBase extends TestCase
{

    /**
     * @var HCli
     */
    public $cli;

    public function setUp()
    {
        $this->cli = new HCli();
    }

}
