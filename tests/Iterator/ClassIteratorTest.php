<?php
namespace hanneskod\classtools\Iterator;

use hanneskod\classtools\Tests\MockSplFileInfo;
use hanneskod\classtools\Tests\MockFinder;

class ClassIteratorTest extends \PHPUnit_Framework_TestCase
{
    private static $sut;

    public static function setupBeforeClass()
    {
        MockFinder::setIterator(
            new \ArrayIterator([
                new MockSplFileInfo('<?php use \\some\\name; class A {}'),
                new MockSplFileInfo('<?php interface TestInterface {}'),
                new MockSplFileInfo('<?php class B implements TestInterface {}'),
                new MockSplFileInfo('<?php class C extends A implements TestInterface {}')
            ])
        );

        self::$sut = new ClassIterator(new MockFinder);
        self::$sut->enableAutoloading();
    }

    public function getSystemUnderTest()
    {
        return self::$sut;
    }

    public function testGetClassmap()
    {
        $this->assertArrayHasKey(
            'A',
            $this->getSystemUnderTest()->getClassMap()
        );

        $this->assertInstanceOf(
            '\SplFileInfo',
            $this->getSystemUnderTest()->getClassMap()['A'],
            'getClassMap should map classnames to SplFileInfo objects'
        );
    }

    public function testExceptionWhenIteratingOverUnloadedClasses()
    {
        $stub = $this->getMockBuilder('hanneskod\classtools\Iterator\ClassIterator')
            ->disableOriginalConstructor()
            ->setMethods(['getClassMap'])
            ->getMock();

        $stub->expects($this->once())
            ->method('getClassMap')
            ->will($this->returnValue(['ClassThatDoesNotExist' => null]));

        $this->setExpectedException('hanneskod\classtools\Exception\LogicException');
        iterator_to_array($stub);
    }

    public function testGetIterator()
    {
        $this->assertArrayHasKey(
            'A',
            iterator_to_array($this->getSystemUnderTest())
        );

        $this->assertInstanceOf(
            '\ReflectionClass',
            iterator_to_array($this->getSystemUnderTest())['A'],
            'getIterator should map classnames to ReflectionClass objects'
        );
    }

    public function testFilteredClassMap()
    {
        $classIterator = $this->getSystemUnderTest();

        $this->assertArrayHasKey(
            'A',
            $classIterator->getClassMap(),
            'A is definied and should be present'
        );

        $this->assertArrayNotHasKey(
            'A',
            $classIterator->where('isInterface')->getClassMap(),
            'A is not an interface and should be filtered'
        );
    }

    public function testTypeFilter()
    {
        $classIterator = $this->getSystemUnderTest();

        $result = iterator_to_array(
            $classIterator->type('TestInterface')
        );

        $this->assertArrayNotHasKey(
            'A',
            $result,
            'A does not implement TestInterface'
        );

        $this->assertArrayHasKey(
            'B',
            $result,
            'B does implement TestInterface'
        );

        $result = iterator_to_array(
            $classIterator
                ->type('TestInterface')
                ->type('A')
        );

        $this->assertArrayNotHasKey(
            'B',
            $result,
            'B does not extend A'
        );

        $this->assertArrayHasKey(
            'C',
            $result,
            'C extends all'
        );
    }

    public function testNameFilter()
    {
        $classIterator = $this->getSystemUnderTest();

        $result = iterator_to_array(
            $classIterator->name('/A/')
        );

        $this->assertArrayNotHasKey(
            'TestInterface',
            $result
        );

        $this->assertArrayHasKey(
            'A',
            $result
        );

        $result = iterator_to_array(
            $classIterator->name('/Test/')->name('/Interface/')
        );

        $this->assertArrayNotHasKey(
            'A',
            $result
        );

        $this->assertArrayHasKey(
            'TestInterface',
            $result
        );
    }

    public function testWhereFilter()
    {
        $classIterator = $this->getSystemUnderTest();

        $result = iterator_to_array(
            $classIterator->where('isInterface')
        );

        $this->assertArrayNotHasKey(
            'A',
            $result,
            'A is not an interface'
        );

        $this->assertArrayHasKey(
            'TestInterface',
            $result,
            'TestInterface is an interface'
        );
    }

    public function testNotFilter()
    {
        $classIterator = $this->getSystemUnderTest();

        $result = iterator_to_array(
            $classIterator->not($classIterator->where('isInterface'))
        );

        $this->assertArrayNotHasKey(
            'TestInterface',
            $result,
            'TestInterface is an interface (and thus not included using the not filter)'
        );

        $this->assertArrayHasKey(
            'A',
            $result,
            'A is not an interface (and thus included using the not filter)'
        );
    }

    public function testCacheFilter()
    {
        $classIterator = $this->getSystemUnderTest();

        $this->assertNotSame(
            $classIterator->getIterator(),
            $classIterator->getIterator()
        );

        $classIterator = $classIterator->cache();

        $this->assertSame(
            $classIterator->getIterator(),
            $classIterator->getIterator()
        );
    }

    public function testMinimize()
    {
        $expected = <<<EOL
<?php namespace  {
    class A
    {
    }
}
namespace  {
    interface TestInterface
    {
    }
}
namespace  {
    class B implements \\TestInterface
    {
    }
}
namespace  {
    class C extends \\A implements \\TestInterface
    {
    }
}

EOL;

        $this->assertEquals(
            $expected,
            $this->getSystemUnderTest()->minimize()
        );
    }
}
