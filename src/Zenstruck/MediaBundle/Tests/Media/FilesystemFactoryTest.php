<?php

namespace Zenstruck\MediaBundle\Tests\Media;

use Symfony\Component\HttpFoundation\Request;
use Zenstruck\MediaBundle\Media\Alert\NullAlertProvider;
use Zenstruck\MediaBundle\Media\FilesystemFactory;
use Zenstruck\MediaBundle\Media\FilesystemManager;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetManager()
    {
        $factory = new FilesystemFactory();
        $factory->addManager('default', new FilesystemManager('default', '/tmp', '/files', new NullAlertProvider()));

        $this->assertCount(1, $factory->getManagerNames());
        $this->assertEquals(array('default'), $factory->getManagerNames());
        $this->assertEquals('default', $factory->getManager()->getName());
        $this->assertEquals('default', $factory->getManager()->getName('default'));
        $this->assertEquals('default', $factory->getManager()->getName('foo'));

        $request = new Request();
        $this->assertEquals('default', $factory->getManager()->getName($request));

        $request = new Request(array('filesystem' => 'default'));
        $this->assertEquals('default', $factory->getManager()->getName($request));

        $request = new Request(array('filesystem' => 'foo'));
        $this->assertEquals('default', $factory->getManager()->getName($request));

        $factory->addManager('second', new FilesystemManager('second', '/tmp', '/files', new NullAlertProvider()));

        $this->assertCount(2, $factory->getManagerNames());
        $this->assertEquals('default', $factory->getManager()->getName());
        $this->assertEquals('default', $factory->getManager()->getName('foo'));
        $this->assertEquals('second', $factory->getManager('second')->getName());

        $request = new Request();
        $this->assertEquals('default', $factory->getManager($request)->getName());

        $request = new Request(array('filesystem' => 'default'));
        $this->assertEquals('default', $factory->getManager($request)->getName());

        $request = new Request(array('filesystem' => 'second'));
        $this->assertEquals('second', $factory->getManager($request)->getName());
    }
}