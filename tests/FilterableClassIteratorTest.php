<?php
namespace hanneskod\classtools;

class FilterableClassIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIteratorReturnsReflectionclass()
    {
        $result = iterator_to_array(
            new FilterableClassIterator(new ClassIterator(__FILE__))
        );

        $this->assertEquals(
            new \ReflectionClass(__CLASS__),
            $result[__CLASS__]
        );
    }

    public function testFilterType()
    {
        $it = new FilterableClassIterator(new ClassIterator(__DIR__.'/../src'));

        $result = iterator_to_array(
            $it->filterType('IteratorAggregate')
        );

        $this->assertArrayNotHasKey(
            'hanneskod\classtools\Minimizer\ClassMinimizer',
            $result,
            'ClassMinimizer does not implement IteratorAggregate'
        );
        $this->assertArrayHasKey(
            'hanneskod\classtools\ClassIterator',
            $result,
            'ClassIterator does implement IteratorAggregate'
        );

        $result = iterator_to_array(
            $it->filterType('IteratorAggregate')->filterType('hanneskod\classtools\FilterableClassIterator')
        );

        $this->assertArrayNotHasKey(
            'hanneskod\classtools\ClassIterator',
            $result,
            'ClassIterator does not extend FilterableClassIterator'
        );
        $this->assertArrayHasKey(
            'hanneskod\classtools\FilterableClassIterator',
            $result,
            'FilterableClassIterator is FilterableClassIterator'
        );
    }

    public function testFilterName()
    {
        $it = new FilterableClassIterator(new ClassIterator(__DIR__.'/../src'));

        $result = iterator_to_array(
            $it->filterName('/Class/')
        );

        $this->assertArrayNotHasKey(
            'hanneskod\classtools\Filter\NameFilter',
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
            'hanneskod\classtools\ClassIterator',
            $result
        );
    }

    public function testWhereFilter()
    {
        $it = new FilterableClassIterator(new ClassIterator(__DIR__.'/../src'));

        $result = iterator_to_array(
            $it->where('isInterface')
        );

        $this->assertArrayNotHasKey(
            'hanneskod\classtools\Filter\NameFilter',
            $result,
            'NameFilter is not an interface'
        );
        $this->assertArrayHasKey(
            'hanneskod\classtools\Filter\FilterInterface',
            $result,
            'FilterInterface is an interface'
        );
    }

    public function testNotFilter()
    {
        $it = new FilterableClassIterator(new ClassIterator(__DIR__.'/../src'));

        $result = iterator_to_array($it->not($it->where('isInterface')));

        $this->assertArrayHasKey(
            'hanneskod\classtools\Filter\NameFilter',
            $result,
            'NameFilter is not an interface (and thus included using the not filter)'
        );
        $this->assertArrayNotHasKey(
            'hanneskod\classtools\Filter\FilterInterface',
            $result,
            'FilterInterface is an interface (and thus not included using the not filter)'
        );
    }

    public function testCacheFilter()
    {
        $it = new FilterableClassIterator(new ClassIterator(__DIR__.'/../src'));

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
