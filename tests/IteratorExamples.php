<?php
namespace hanneskod\classtools;

/**
 * Iterator examples
 */
class IteratorExamples extends \hanneskod\exemplify\TestCase
{
    /**
     * Iterate over classes in project
     *
     * @expectOutputRegex #classtools/src/ClassIterator.php$#
     */
    public function exampleClassIterator()
    {
        $pathToClasstools = __DIR__.'/../src';
        $classIterator = new ClassIterator($pathToClasstools);

        $arrayOfClassesInProject = iterator_to_array($classIterator);

        // prints path to hanneskod\classtools\ClassIterator
        echo $arrayOfClassesInProject['hanneskod\classtools\ClassIterator'];
    }

    /**
     * Find classes based on type
     *
     * @expectOutputRegex /^Array/
     */
    public function exampleFilterType()
    {
        $pathToClasstools = __DIR__.'/../src';
        $classIterator = new ClassIterator($pathToClasstools);
        $filterableIterator = new FilterableClassIterator($classIterator);

        // prints all FilterInterface types (including the interface itself)
        print_r(
            iterator_to_array(
                $filterableIterator->filterType('hanneskod\classtools\Filter\FilterInterface')
            )
        );

        // prints instantiable classes that implement FilterInterface
        print_r(
            iterator_to_array(
                $filterableIterator
                    ->filterType('hanneskod\classtools\Filter\FilterInterface')
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
        $pathToClasstools = __DIR__.'/../src';
        $classIterator = new ClassIterator($pathToClasstools);
        $filterableIterator = new FilterableClassIterator($classIterator);

        // prints all classes in the Filter namespace
        print_r(
            iterator_to_array(
                $filterableIterator
                    ->filterName('/^hanneskod\\\classtools\\\Filter\\\/')
                    ->where('isInstantiable')
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
        $pathToClasstools = __DIR__.'/../src';
        $classIterator = new ClassIterator($pathToClasstools);
        $filterableIterator = new FilterableClassIterator($classIterator);

        // prints all classes NOT in the Filter namespace
        print_r(
            iterator_to_array(
                $filterableIterator
                    ->not(
                        $filterableIterator->filterName('/^hanneskod\\\classtools\\\Filter\\\/')
                    )
                    ->where('isInstantiable')
            )
        );
    }
}
