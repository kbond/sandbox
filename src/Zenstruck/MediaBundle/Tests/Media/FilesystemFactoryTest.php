<?php

namespace Zenstruck\MediaBundle\Tests\Media;

use Symfony\Component\HttpFoundation\Request;
use Zenstruck\MediaBundle\Media\Alert\NullAlertProvider;
use Zenstruck\MediaBundle\Media\FilesystemFactory;
use Zenstruck\MediaBundle\Media\Permission\TruePermissionProvider;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class FilesystemFactoryTest extends BaseFilesystemTest
{
    public function testAddManagerBadConfig()
    {
        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\MissingOptionsException');
        $factory = new FilesystemFactory(new NullAlertProvider());
        $factory->addManager('default', array());
    }

    public function testGetManager()
    {
        $factory = new FilesystemFactory(new NullAlertProvider());
        $factory->addManager('default', array(
                'root_dir' => '/tmp',
                'web_prefix' => '/files'
            ));

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

        $factory->addManager('second', array(
            'root_dir' => '/tmp',
            'web_prefix' => '/files'
        ));

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