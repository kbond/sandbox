<?php

namespace Zenstruck\MediaBundle\Tests\Media;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemManagerTest extends BaseFilesystemTest
{
    public function testGetAncestry()
    {
        $filesystem = $this->createFilesystemManager();

        $this->assertCount(0, $filesystem->getAncestry());

        $filesystem = $this->createFilesystemManager('default', 'A');
        $this->assertCount(1, $filesystem->getAncestry());

        $filesystem = $this->createFilesystemManager('default', 'A/B');
        $this->assertCount(2, $filesystem->getAncestry());
    }

    public function testGetPreviousPath()
    {
        $filesystem = $this->createFilesystemManager();

        $this->assertEmpty($filesystem->getPreviousPath());

        $filesystem = $this->createFilesystemManager('default', 'A');
        $this->assertEmpty($filesystem->getPreviousPath());

        $filesystem = $this->createFilesystemManager('default', 'A/B');
        $this->assertEquals('A', $filesystem->getPreviousPath());

        $filesystem = $this->createFilesystemManager('default', 'A/B/C');
        $this->assertEquals('A/B', $filesystem->getPreviousPath());
    }
}