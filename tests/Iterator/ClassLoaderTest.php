<?php
namespace hanneskod\classtools\Iterator;

use hanneskod\classtools\Tests\MockSplFileInfo;

class ClassLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testClassLoader()
    {
        $classLoader = new ClassLoader([
            'UnloadedClass' => new MockSplFileInfo(
                '<?php class UnloadedClass { function foo(){return "bar";} }'
            )
        ]);

        $classLoader->register();

        $unloadedClass = new \UnloadedClass;

        $this->assertEquals('bar', $unloadedClass->foo());

        $classLoader->unregister();
    }
}
