<?php

namespace Zenstruck\MediaBundle\Tests\Media;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\MediaBundle\Media\Alert\NullAlertProvider;
use Zenstruck\MediaBundle\Media\File;
use Zenstruck\MediaBundle\Media\FilesystemManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider pathProvider
     */
    public function testWorkingDir($path, $actualpath)
    {
        $filesystem = $this->createFilesystemManager();

        $filesystem->configure($path);
        $this->assertEquals($this->getTempFixtureDir().$actualpath, $filesystem->getWorkingDir());
    }

    public function testWorkingDirWithoutSlash()
    {
        $filesystem = new FilesystemManager(sys_get_temp_dir().'/Fixures', '/files', new NullAlertProvider());
        $filesystem->configure('copy/A');
        $this->assertEquals($this->getTempFixtureDir().'copy/A/', $filesystem->getWorkingDir());
    }

    public function testGetFiles()
    {
        $filesystem = $this->createFilesystemManager();
        $filesystem->configure();

        /** @var File[] $files */
        $files = $filesystem->getFiles();

        $this->assertCount(6, $files);
        $this->assertEquals('A', $files[0]->getFilename());
        $this->assertTrue($files[0]->isDir());
        $this->assertEquals('dolor.txt', $files[3]->getFilename());
        $this->assertFalse($files[3]->isDir());
        $this->assertTrue($files[3]->isFile());
        $this->assertEquals('/files/dolor.txt', $files[3]->getWebPath());

        $filesystem = $this->createFilesystemManager();
        $filesystem->configure('with space');

        /** @var File[] $files */
        $files = $filesystem->getFiles();
        $this->assertCount(1, $files);
        $this->assertEquals('foo.txt', $files[0]->getFilename());
        $this->assertEquals('/files/with space/foo.txt', $files[0]->getWebPath());

        $filesystem = $this->createFilesystemManager('/');
        $filesystem->configure('with space');
        $files = $filesystem->getFiles();
        $this->assertEquals('/with space/foo.txt', $files[0]->getWebPath());
    }

    public function testGetAncestry()
    {
        $filesystem = $this->createFilesystemManager();
        $filesystem->configure();

        $this->assertCount(0, $filesystem->getAncestry());

        $filesystem->configure('A');
        $this->assertCount(1, $filesystem->getAncestry());

        $filesystem->configure('A/B');
        $this->assertCount(2, $filesystem->getAncestry());
    }

    public function testGetPreviousPath()
    {
        $filesystem = $this->createFilesystemManager();
        $filesystem->configure();

        $this->assertEmpty($filesystem->getPreviousPath());

        $filesystem->configure('A');
        $this->assertEmpty($filesystem->getPreviousPath());

        $filesystem->configure('A/B');
        $this->assertEquals('A', $filesystem->getPreviousPath());

        $filesystem->configure('A/B/C');
        $this->assertEquals('A/B', $filesystem->getPreviousPath());
    }

    public function testRenameFile()
    {
        $filesystem = $this->createFilesystemManager();
        $filesystem->renameFile(null, 'dolor.txt', 'foo.txt');

        $this->assertFileExists($this->getTempFixtureDir().'foo.txt');
        $this->assertFileNotExists($this->getTempFixtureDir().'dolor.txt');

        $filesystem->renameFile(null, 'ipsum.txt', 'bar.dat');
        $this->assertFileExists($this->getTempFixtureDir().'bar.txt');

        $filesystem = $this->createFilesystemManager();
        $filesystem->renameFile('A', 'a.dat', 'foo.dat');

        $this->assertFileExists($this->getTempFixtureDir().'A/foo.dat');
        $this->assertTrue(is_file($this->getTempFixtureDir().'A/foo.dat'));

        $filesystem->renameFile('A', 'B', 'Foo');

        $this->assertFileExists($this->getTempFixtureDir().'A/Foo');
        $this->assertTrue(is_dir($this->getTempFixtureDir().'A/Foo'));
    }

    public function testDeleteFile()
    {
        $filesystem = $this->createFilesystemManager();

        $this->assertFileExists($this->getTempFixtureDir().'dolor.txt');
        $filesystem->deleteFile(null, 'dolor.txt');
        $this->assertFileNotExists($this->getTempFixtureDir().'dolor.txt');

        $this->assertFileExists($this->getTempFixtureDir().'A/B');
        $filesystem->deleteFile('A', 'B');
        $this->assertFileNotExists($this->getTempFixtureDir().'A/B');
    }

    public function testMkDir()
    {
        $filesystem = $this->createFilesystemManager();

        $this->assertFileNotExists($this->getTempFixtureDir().'foo');
        $filesystem->mkDir(null, 'foo');
        $this->assertFileExists($this->getTempFixtureDir().'foo');

        $this->assertFileNotExists($this->getTempFixtureDir().'A/foo');
        $filesystem->mkDir('A', 'foo');
        $this->assertFileExists($this->getTempFixtureDir().'A/foo');
    }

    public function testUploadFile()
    {
        $filesystem = $this->createFilesystemManager();
        $tempFile = sys_get_temp_dir().'/foo.txt';
        touch($tempFile);

        $file = new UploadedFile($tempFile, 'foo.txt', null, null, null, true);

        $this->assertFileNotExists($this->getTempFixtureDir().'foo.txt');
        $filesystem->uploadFile(null, $file);
        $this->assertFileExists($this->getTempFixtureDir().'foo.txt');

        touch($tempFile);

        $this->assertFileNotExists($this->getTempFixtureDir().'A/foo.txt');
        $filesystem->uploadFile('A', $file);
        $this->assertFileExists($this->getTempFixtureDir().'A/foo.txt');
    }

    public function testDirectoryNotFoundException()
    {
        $this->setExpectedException('Zenstruck\MediaBundle\Exception\DirectoryNotFoundException');
        $filesystem = $this->createFilesystemManager();

        $filesystem->configure('foo');
    }

    public function pathProvider()
    {
        return array(
            array(null, null),
            array('/', null),
            array('/copy', 'copy/'),
            array('copy', 'copy/'),
            array('copy/A', 'copy/A/'),
            array('copy/A/', 'copy/A/'),
            array('/copy/A', 'copy/A/'),
            array('with space', 'with space/')
        );
    }

    protected function setUp()
    {
        $filesystem = new Filesystem();
        $filesystem->mirror(__DIR__.'/../Fixtures', $this->getTempFixtureDir());
    }

    protected function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getTempFixtureDir());
    }

    protected function createFilesystemManager($webPrefix = '/files')
    {
        return new FilesystemManager($this->getTempFixtureDir(), $webPrefix, new NullAlertProvider());
    }

    protected function getTempFixtureDir()
    {
        return sys_get_temp_dir().'/Fixures/';
    }
}