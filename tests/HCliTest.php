<?php
declare(strict_types=1);
namespace hcore\cli\Tests;
use PHPUnit\Framework\TestCase;
final class HCliTest extends TestCase
{
    protected $testDir = __DIR__;

    public function testWritableFolder(): void
    {
        $this->assertDirectoryIsWritable($this->testDir);
    }
}
