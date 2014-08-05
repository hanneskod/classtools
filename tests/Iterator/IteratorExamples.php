<?php
namespace hanneskod\classtools\Iterator;

define('PATH_TO_CLASSTOOLS', __DIR__.'/../../src');

/**
 * Iterator examples
 */
class IteratorExamples extends \hanneskod\exemplify\TestCase
{
    /**
     * Iterate over classes in project
     *
     * @expectOutputRegex #classtools/src/Iterator/ClassIterator.php$#
     */
    public function exampleClassIterator()
    {
        $classes = iterator_to_array(new ClassIterator(PATH_TO_CLASSTOOLS));

        // prints path to hanneskod\classtools\Iterator\ClassIterator
        echo $classes['hanneskod\classtools\Iterator\ClassIterator'];
    }

    /**
     * Find classes based on type
     *
     * @expectOutputRegex /^Array/
     */
    public function exampleFilterType()
    {
        $filterableIterator = new FilterableClassIterator(
            new ClassIterator(PATH_TO_CLASSTOOLS)
        );

        // prints all FilterInterface types (including the interface itself)
        print_r(
            iterator_to_array(
                $filterableIterator->filterType('hanneskod\classtools\Iterator\Filter\FilterInterface')
            )
        );

        // prints instantiable classes that implement FilterInterface
        print_r(
            iterator_to_array(
                $filterableIterator
                    ->filterType('hanneskod\classtools\Iterator\Filter\FilterInterface')
                    ->where('isInstantiable')
            )
        );
    }

    /**
     * Find classes based on name
     *
     * @expectOutputRegex /^Array/
     */
    public function exampleFilterName()
    {
        $filterableIterator = new FilterableClassIterator(
            new ClassIterator(PATH_TO_CLASSTOOLS)
        );

        // prints classes and interfaces in the Filter namespace
        print_r(
            iterator_to_array(
                $filterableIterator
                    ->filterName('/^hanneskod\\\classtools\\\Iterator\\\Filter\\\/')
            )
        );
    }

    /**
     * Negate filters
     *
     * @expectOutputRegex /^Array/
     */
    public function exampleFilterNot()
    {
        $filterableIterator = new FilterableClassIterator(
            new ClassIterator(PATH_TO_CLASSTOOLS)
        );

        // prints all classes and interfaces NOT in the Filter namespace
        print_r(
            iterator_to_array(
                $filterableIterator
                    ->not(
                        $filterableIterator->filterName('/^hanneskod\\\classtools\\\Iterator\\\Filter\\\/')
                    )
            )
        );
    }
}
