<?php
namespace hanneskod\classtools\Loader;

use hanneskod\classtools\Tests\MockSplFileInfo;

class ClassLoaderTest extends \PHPUnit\Framework\TestCase
{
    public function testClassLoader()
    {
        $iterator = $this->getMockBuilder('hanneskod\classtools\Iterator\ClassIterator')
            ->disableOriginalConstructor()
            ->getMock();

        $iterator->expects($this->once())
            ->method('getClassMap')
            ->will($this->returnValue([
                'UnloadedClass' => new MockSplFileInfo(
                    '<?php class UnloadedClass { function foo(){return "bar";} }'
                )
            ]));

        $loader = new ClassLoader($iterator, true);

        $unloadedClass = new \UnloadedClass;
        $this->assertEquals('bar', $unloadedClass->foo());

        $loader->unregister();
    }
}
