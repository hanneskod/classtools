<?php
namespace hanneskod\classtools\Iterator;

use hanneskod\classtools\Tests\MockSplFileInfo;
use hanneskod\classtools\Tests\MockFinder as Finder;
use hanneskod\classtools\Transformer\MinimizingWriter;

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
        foreach ($iter->getClassMap() as $classname => $splFileInfo) {
            echo $classname.': '.$splFileInfo->getRealPath();
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
     * Note that to use reflection the classes found in filesystem must be included
     * in the environment. Enable autoloading to dynamically load classes from a
     * ClassIterator.
     *
     * @expectOutputString Iterator\FilterIterator\NotFilterIterator\ClassIterator
     */
    public function exampleIterator()
    {
        $finder = new Finder();
        $iter = new ClassIterator($finder->in('src'));

        // Enable reflection by autoloading found classes
        $iter->enableAutoloading();

        // Print all classes, interfaces and traits in 'src'
        foreach ($iter as $class) {
            echo $class->getName();
        }
    }

    /**
     * Filter based on class properties
     *
     * [ClassIterator](src/Iterator/ClassIterator.php) is filterable and filters
     * are chainable.
     *
     * @expectOutputString Iterator\FilterIterator\NotFilterIterator\ClassIteratorIterator\NotFilter
     */
    public function exampleFilter()
    {
        $finder = new Finder();
        $iter = new ClassIterator($finder->in('src'));
        $iter->enableAutoloading();

        // Print all Filter types (including the interface itself)
        foreach ($iter->type('Iterator\Filter') as $class) {
            echo $class->getName();
        }

        // Print definitions in the Iterator namespace whose name contains 'Class'
        foreach ($iter->inNamespace('Iterator')->name('/Class/') as $class) {
            echo $class->getName();
        }

        // Print implementations of the Filter interface
        foreach ($iter->type('Iterator\Filter')->where('isInstantiable') as $class) {
            echo $class->getName();
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
        $iter->enableAutoloading();

        // Print all classes, interfaces and traits NOT instantiable
        foreach ($iter->not($iter->where('isInstantiable')) as $class) {
            echo $class->getName();
        }
    }

    /**
     * Transforming classes
     *
     * Found class, interface and trait definitions can be transformed and
     * written to a single file.
     *
     * @expectOutputRegex /^\<\?php/
     */
    public function exampleMinimize()
    {
        $finder = new Finder();
        $iter = new ClassIterator($finder->in('src'));
        $iter->enableAutoloading();

        // Print all found definitions in one snippet
        echo $iter->minimize();

        // The same can be done using
        echo $iter->transform(new MinimizingWriter);
    }
}
