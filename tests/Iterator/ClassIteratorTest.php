<?php
namespace hanneskod\classtools\Iterator;

class ClassIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testNoConstructArgs()
    {
        $this->assertEmpty(
            iterator_to_array(new ClassIterator),
            'No arguments to constructor should yield no found classes'
        );
    }

    public function testInvalidConstructorArgs()
    {
        $this->setExpectedException('hanneskod\classtools\Exception\RuntimeException');
        new ClassIterator(array('not-a-file-or-dir'));
    }

    public function testScanFile()
    {
        $this->assertArrayHasKey(
            __CLASS__,
            iterator_to_array(new ClassIterator(__FILE__))
        );
    }

    public function testScanDir()
    {
        $this->assertArrayHasKey(
            __CLASS__,
            iterator_to_array(new ClassIterator(array(__DIR__)))
        );
    }

    public function testGetClassmap()
    {
        $iter = new ClassIterator(__FILE__);
        $this->assertArrayHasKey(
            __CLASS__,
            $iter->getClassMap()
        );
    }

    public function testFilteredClassMap()
    {
        $it = new ClassIterator(__DIR__.'/../../src');

        $resultFilter = iterator_to_array(
            $it->where('isInterface')->getClassMap()
        );

        $resultNoFilter = $it->getClassMap();

        $this->assertTrue(is_string($resultFilter['hanneskod\classtools\Iterator\Filter']));
        $this->assertTrue(is_string($resultNoFilter['hanneskod\classtools\Iterator\Filter']));
    }

    public function testIteratorReturnsReflectionclass()
    {
        $result = iterator_to_array(new ClassIterator(__FILE__));

        $this->assertEquals(
            new \ReflectionClass(__CLASS__),
            $result[__CLASS__]
        );
    }

    public function testTypeFilter()
    {
        $it = new ClassIterator(__DIR__.'/../../src');

        $result = iterator_to_array(
            $it->filterType('IteratorAggregate')
        );

        $this->assertArrayNotHasKey(
            'hanneskod\classtools\Iterator\Filter',
            $result,
            'Filter does not implement IteratorAggregate'
        );

        $this->assertArrayHasKey(
            'hanneskod\classtools\Iterator\ClassIterator',
            $result,
            'ClassIterator does implement IteratorAggregate'
        );

        $result = iterator_to_array(
            $it
                ->filterType('hanneskod\classtools\Iterator\ClassIterator')
                ->filterType('hanneskod\classtools\Iterator\Filter')
        );

        $this->assertArrayNotHasKey(
            'hanneskod\classtools\Iterator\Filter',
            $result,
            'Filter does not extend ClassIterator'
        );

        $this->assertArrayHasKey(
            'hanneskod\classtools\Iterator\Filter\CacheFilter',
            $result,
            'CacheFilter extends all'
        );
    }

    public function testNameFilter()
    {
        $it = new ClassIterator(__DIR__.'/../../src');

        $result = iterator_to_array(
            $it->filterName('/Class/')
        );

        $this->assertArrayNotHasKey(
            'hanneskod\classtools\Iterator\Filter\NameFilter',
            $result
        );
        $this->assertArrayHasKey(
            'hanneskod\classtools\Minimizer\ClassMinimizer',
            $result
        );

        $result = iterator_to_array(
            $it->filterName('/Class/')->filterName('/Iterator/')
        );

        $this->assertArrayNotHasKey(
            'hanneskod\classtools\Minimizer\ClassMinimizer',
            $result
        );
        $this->assertArrayHasKey(
            'hanneskod\classtools\Iterator\ClassIterator',
            $result
        );
    }

    public function testWhereFilter()
    {
        $it = new ClassIterator(__DIR__.'/../../src');

        $result = iterator_to_array(
            $it->where('isInterface')
        );

        $this->assertArrayNotHasKey(
            'hanneskod\classtools\Iterator\Filter\NameFilter',
            $result,
            'NameFilter is not an interface'
        );
        $this->assertArrayHasKey(
            'hanneskod\classtools\Iterator\Filter',
            $result,
            'Filter is an interface'
        );
    }

    public function testNotFilter()
    {
        $it = new ClassIterator(__DIR__.'/../../src');

        $result = iterator_to_array($it->not($it->where('isInterface')));

        $this->assertArrayHasKey(
            'hanneskod\classtools\Iterator\Filter\NameFilter',
            $result,
            'NameFilter is not an interface (and thus included using the not filter)'
        );
        $this->assertArrayNotHasKey(
            'hanneskod\classtools\Iterator\Filter',
            $result,
            'Filter is an interface (and thus not included using the not filter)'
        );
    }

    public function testCacheFilter()
    {
        $it = new ClassIterator(__DIR__.'/../../src');

        $this->assertNotSame(
            $it->getIterator(),
            $it->getIterator()
        );

        $it = $it->cache();

        $this->assertSame(
            $it->getIterator(),
            $it->getIterator()
        );
    }
}
