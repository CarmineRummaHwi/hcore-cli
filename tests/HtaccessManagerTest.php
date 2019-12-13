<?php


use hcore\cli\HtaccessManager;
use hcore\cli\Utilities;
use PHPUnit\Framework\TestCase;

class HtaccessManagerTest extends TestCase
{
    public function testAddToBanlist()
    {
        if (!file_exists(".htaccess")) {
            Utilities::copyResource("../src/resources/htaccess", ".htaccess");
        }

        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->addToBanlist("todaperfeita");
        $htaManager->addToBanlist("trikaladay");
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testRemoveFromBanlist()
    {
        if (!file_exists(".htaccess")) {
            Utilities::copyResource("../src/resources/htaccess", ".htaccess");
        }

        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->removeFromBanlist("trikaladay");
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testEnableCache()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->enableHtaccessCache();
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testDisableCache()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->disableHtaccessCache();
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testEnableSecurityCleaner()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->enableSecurityCleaner();
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testDisableSecurityCleaner()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->disableSecurityCleaner();
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testEnableSecurity()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->enableHtaccessSecurity();
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testDisableSecurity()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->disableHtaccessSecurity();
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testEnableSecurityAdvanced()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->enableHtaccessSecurityAdvanced();
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testDisableSecurityAdvanced()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->disableHtaccessSecurityAdvanced();
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testEnableSecurityPolicy()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->enableSecurityPolicy();
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testDisableSecurityPolicy()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->disableSecurityPolicy();
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }

    public function testAddXFrameOption()
    {
        $htaManager = HtaccessManager::getInstance("./.htaccess");
        $htaManager->addXFrameOptions("enzoromano.eu");
        $htaManager->addXFrameOptions("carminerumma.it");
        $htaManager->save();

        $this->assertFileExists(".htaccess", ".htaccess not exists");
    }
}
