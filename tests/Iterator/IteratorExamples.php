<?php
namespace hanneskod\classtools\Iterator;

define('PATH_TO_CLASSTOOLS', __DIR__.'/../../src');

/**
 * Iterator examples
 */
class IteratorExamples extends \hanneskod\exemplify\TestCase
{
    /**
     * Read classes in project
     *
     * @expectOutputRegex #classtools/src/Iterator/ClassIterator.php$#
     */
    public function exampleClassIterator()
    {
        $iter = new ClassIterator(PATH_TO_CLASSTOOLS);

        // prints path to hanneskod\classtools\Iterator\ClassIterator
        echo $iter->getClassMap()['hanneskod\classtools\Iterator\ClassIterator'];
    }

    /**
     * Find classes based on type
     *
     * @expectOutputRegex /^Array/
     */
    public function exampleFilterType()
    {
        $it = new ClassIterator(PATH_TO_CLASSTOOLS);

        // prints all Filter types (including the interface itself)
        print_r(iterator_to_array(
            $it->filterType('hanneskod\classtools\Iterator\Filter')
        ));

        // prints classes that implement the Filter interface
        print_r(iterator_to_array(
            $it->filterType('hanneskod\classtools\Iterator\Filter')
               ->where('isInstantiable')
        ));
    }

    /**
     * Find classes based on name
     *
     * @expectOutputRegex /^Array/
     */
    public function exampleFilterName()
    {
        $it = new ClassIterator(PATH_TO_CLASSTOOLS);

        // prints classes and interfaces in the Filter namespace
        print_r(iterator_to_array(
            $it->filterName('/^hanneskod\\\classtools\\\Iterator\\\Filter\\\/')
        ));
    }

    /**
     * Negate filters
     *
     * @expectOutputRegex /^Array/
     */
    public function exampleFilterNot()
    {
        $it = new ClassIterator(PATH_TO_CLASSTOOLS);

        // prints all classes and interfaces NOT in the Filter namespace
        print_r(iterator_to_array(
            $it->not(
                $it->filterName('/^hanneskod\\\classtools\\\Iterator\\\Filter\\\/')
            )
        ));
    }
}
