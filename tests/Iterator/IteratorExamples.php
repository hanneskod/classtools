<?php
namespace hanneskod\classtools\Iterator;

use hanneskod\classtools\Tests\MockSplFileInfo;
use hanneskod\classtools\Tests\MockFinder as Finder;

/**
 * Iterator
 *
 * [ClassIterator](src/Iterator/ClassIterator.php) consumes a
 * [symfony finder](http://symfony.com/doc/current/components/finder.html)
 * and scans files for php classes, interfaces and traits.
 */
class IteratorExamples extends \hanneskod\exemplify\TestCase
{
    public static function setupBeforeClass()
    {
        Finder::setIterator(
            new \ArrayIterator([
                new MockSplFileInfo('<?php namespace Iterator; interface Filter {}'),
                new MockSplFileInfo('<?php namespace Iterator; class NotFilter implements Filter {}'),
                new MockSplFileInfo('<?php namespace Iterator; class ClassIterator {}')
            ])
        );
    }

    /**
     * Access the class map
     *
     * `getClassMap()` returns a map of class names to
     * [SplFileInfo](http://api.symfony.com/2.5/Symfony/Component/Finder/SplFileInfo.html)
     * objects. 
     *
     * @expectOutputRegex /php$/
     */
    public function exampleGetClassMap()
    {
        $finder = new Finder;
        $iter = new ClassIterator($finder->in('src'));

        // Print the file names of classes, interfaces and traits in 'src'
        foreach ($iter->getClassMap() as $name => $splFileInfo) {
            echo $splFileInfo->getRealPath();
        }
    }

    /**
     * Iterate over ReflectionClass objects
     *
     * ClassIterator is also a [Traversable](http://php.net/manual/en/class.traversable.php),
     * that on iteration yields class names as keys and
     * [ReflectionClass](http://php.net/manual/en/class.reflectionclass.php)
     * objects as values.
     *
     * @expectOutputString Iterator\FilterIterator\NotFilterIterator\ClassIterator
     */
    public function exampleIterator()
    {
        $finder = new Finder();
        $iter = new ClassIterator($finder->in('src'));

        // Prints all classes, interfaces and traits in 'src'
        foreach ($iter as $name => $reflectionClass) {
            echo $name;
        }
    }

    /**
     * Filter based on class properties
     *
     * [ClassIterator](src/Iterator/ClassIterator.php) is filterable and filters
     * are chainable.
     *
     * @expectOutputString Iterator\FilterIterator\NotFilterIterator\FilterIterator\NotFilterIterator\ClassIteratorIterator\NotFilter
     */
    public function exampleFilter()
    {
        $finder = new Finder();
        $iter = new ClassIterator($finder->in('src'));

        // Prints all Filter types (including the interface itself)
        foreach ($iter->type('Iterator\Filter') as $name => $reflectionClass) {
            echo $name;
        }

        // Prints classes, interfaces and traits in the Iterator namespace
        foreach ($iter->name('/Iterator\\\/') as $name => $reflectionClass) {
            echo $name;
        }

        // Prints implementations of the Filter interface
        $iter = $iter->type('Iterator\Filter')->where('isInstantiable');
        foreach ($iter as $name => $reflectionClass) {
            echo $name;
        }
    }

    /**
     * Negate filters
     *
     * Filters can also be negated by wrapping them in `not()` method calls.
     *
     * @expectOutputString Iterator\Filter
     */
    public function exampleNegateFilters()
    {
        $finder = new Finder();
        $iter = new ClassIterator($finder->in('src'));

        // Prints all classes, interfaces and traits NOT instantiable
        foreach ($iter->not($iter->where('isInstantiable')) as $name => $reflectionClass) {
            echo $name;
        }
    }
}
