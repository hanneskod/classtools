<?php
namespace hanneskod\classtools\Iterator;

use Symfony\Component\Finder\Tests\Iterator\MockSplFileInfo;

class ClassIteratorTest extends \PHPUnit_Framework_TestCase
{
    private $sut;

    public function getSystemUnderTest()
    {
        if (!isset($this->sut)) {
            $fileInfoObjects = [
                new MockSplFileInfo([
                    'name' => 'A.php',
                    'contents' => '<?php class A {}'
                ]),
                new MockSplFileInfo([
                    'name' => 'TestInterface.php',
                    'contents' => '<?php interface TestInterface {}'
                ]),
                new MockSplFileInfo([
                    'name' => 'B.php',
                    'contents' => '<?php class B implements TestInterface {}'
                ]),
                new MockSplFileInfo([
                    'name' => 'C.php',
                    'contents' => '<?php class C extends A implements TestInterface {}'
                ])
            ];

            $finder = $this->getMockBuilder('Symfony\Component\Finder\Finder')
                ->disableOriginalConstructor()
                ->getMock();

            $finder->expects($this->any())
                ->method('getIterator')
                ->will($this->returnValue(new \ArrayIterator($fileInfoObjects)));

            $this->sut = new ClassIterator($finder);
        }

        return $this->sut;
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
}
