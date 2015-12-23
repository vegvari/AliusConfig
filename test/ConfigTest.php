<?php

namespace Alius\Config;

use PHPUnit_Framework_TestCase;
use Alius\Config\Exceptions\FileNotFound;
use Alius\Config\Exceptions\InvalidContent;
use Alius\Config\Exceptions\FileNotReadable;
use Alius\Config\Exceptions\InvalidExtension;
use Alius\Config\Exceptions\UndefinedRequired;
use Alius\Config\Exceptions\UndefinedVariable;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testFile()
    {
        $config = new Config(__DIR__ . '/fixtures/test_file.php');
        $this->assertSame('test_file.php', $config->get('test'));
        $this->assertSame(__DIR__ . '/fixtures/test_file.php', $config->getOrigin('test'));

        $this->assertCount(1, $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/test_file.php', $config->getFiles());
    }

    public function testFileWithRequiredConfigVariable()
    {
        $config = new Config(__DIR__ . '/fixtures/test_file.php', ['test']);
        $this->assertSame(true, $config->isRequired('test'));
        $this->assertSame(false, $config->isRequired('test2'));
    }

    public function testFileWithRequiredConfigVariableAfterInstantiated()
    {
        $config = new Config(__DIR__ . '/fixtures/test_file.php');
        $this->assertSame(false, $config->isRequired('test'));
        $config->required('test');
        $this->assertSame(true, $config->isRequired('test'));
    }

    public function testMultipleFiles()
    {
        $config = new Config([__DIR__ . '/fixtures/test_file.php', __DIR__ . '/fixtures/test_file2.php']);
        $this->assertSame('test_file2.php', $config->get('test'));
        $this->assertSame(__DIR__ . '/fixtures/test_file2.php', $config->getOrigin('test'));

        $this->assertCount(2, $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/test_file.php', $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/test_file2.php', $config->getFiles());
    }

    public function testDir()
    {
        $config = new Config(__DIR__ . '/fixtures/recursive');
        $this->assertSame('recursive/recursive/test_file2.php', $config->get('test'));
        $this->assertSame(__DIR__ . '/fixtures/recursive/recursive/test_file2.php', $config->getOrigin('test'));

        $this->assertCount(2, $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/recursive/recursive/test_file.php', $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/recursive/recursive/test_file2.php', $config->getFiles());
    }

    public function testDirTrailingSlash()
    {
        $config = new Config(__DIR__ . '/fixtures/recursive/');
        $this->assertSame('recursive/recursive/test_file2.php', $config->get('test'));
        $this->assertSame(__DIR__ . '/fixtures/recursive/recursive/test_file2.php', $config->getOrigin('test'));

        $this->assertCount(2, $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/recursive/recursive/test_file.php', $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/recursive/recursive/test_file2.php', $config->getFiles());
    }

    public function testMultipleDirs()
    {
        $config = new Config([__DIR__ . '/fixtures/recursive', __DIR__ . '/fixtures/recursive2']);
        $this->assertSame('recursive2/recursive/test_file2.php', $config->get('test'));
        $this->assertSame(__DIR__ . '/fixtures/recursive2/recursive/test_file2.php', $config->getOrigin('test'));

        $this->assertCount(4, $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/recursive/recursive/test_file.php', $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/recursive/recursive/test_file2.php', $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/recursive2/recursive/test_file.php', $config->getFiles());
        $this->assertArrayHasKey(__DIR__ . '/fixtures/recursive2/recursive/test_file2.php', $config->getFiles());
    }

    public function testCompile()
    {
        $config = new Config([__DIR__ . '/fixtures/test_file.php', __DIR__ . '/fixtures/test_file2.php']);

        $this->assertCount(1, $config->compile());
        $this->assertSame(['test' => 'test_file2.php'], $config->compile());
    }

    public function testNotFound()
    {
        $this->setExpectedException(FileNotFound::class);
        new Config(__DIR__ . '/fixtures/test_file_not_exist.php');
    }

    public function testNotReadable()
    {
        $this->setExpectedException(FileNotReadable::class);
        new Config(__DIR__ . '/fixtures/test_file_not_readable.php');
    }

    public function testWrongExtension()
    {
        $this->setExpectedException(InvalidExtension::class);
        new Config(__DIR__ . '/fixtures/test_file_not_php.txt');
    }

    public function testContentIsNotArray()
    {
        $this->setExpectedException(InvalidContent::class);
        new Config(__DIR__ . '/fixtures/test_file_not_array.php');
    }

    public function testMissingRequired()
    {
        $this->setExpectedException(UndefinedRequired::class);
        new Config(__DIR__ . '/fixtures/test_file.php', ['test2']);
    }

    public function testMissingRequiredAfterInstantiated()
    {
        $this->setExpectedException(UndefinedRequired::class);
        $config = new Config(__DIR__ . '/fixtures/test_file.php');
        $config->required('test2');
    }

    public function testMissingVariable()
    {
        $this->setExpectedException(UndefinedVariable::class);
        $config = new Config(__DIR__ . '/fixtures/test_file.php');
        $config->get('test2');
    }

    public function testMissingVariableOrigin()
    {
        $this->setExpectedException(UndefinedVariable::class);
        $config = new Config(__DIR__ . '/fixtures/test_file.php');
        $config->getOrigin('test2');
    }
}

/**
 * Replace the global is_readable function to test not readable files
 *
 * @param  string $file
 * @return bool
 */
function is_readable($file)
{
    return $file === __DIR__ . '/fixtures/test_file_not_readable.php' ? false : \is_readable($file);
}
