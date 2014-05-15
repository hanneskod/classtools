<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools;

use ReflectionClass;
use hanneskod\classtools\Filter\FilterInterface;
use hanneskod\classtools\Filter\CacheFilter;
use hanneskod\classtools\Filter\NameFilter;
use hanneskod\classtools\Filter\NotFilter;
use hanneskod\classtools\Filter\TypeFilter;
use hanneskod\classtools\Filter\WhereFilter;

/**
 * Iterate over classes found in filesystem and get ReflectionClass objects
 *
 * Iterator yields classnames as keys and ReflectionClass objects as values
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class FilterableClassIterator implements FilterableInterface
{
    private $classIterator;

    /**
     * @param ClassIterator $classIterator
     */
    public function __construct(ClassIterator $classIterator)
    {
        $this->classIterator = $classIterator;
    }

    /**
     * Iterator yields classnames as keys and ReflectionClass objects as values
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        foreach ($this->classIterator as $className => $path) {
            yield $className => new ReflectionClass($className);
        }
    }

    /**
     * Bind filter to iterator
     *
     * @param  FilterInterface $filter
     * @return FilterInterface The bound filter
     */
    public function filter(FilterInterface $filter)
    {
        $filter->bindTo($this);
        return $filter;
    }

    /**
     * Create a new iterator where classes are filtered based on type
     *
     * @param  string $typename
     * @return FilterableInterface
     */
    public function filterType($typename)
    {
        return $this->filter(new TypeFilter($typename));
    }

    /**
     * Create a new iterator where classes are filtered based on name
     *
     * @param  string $pattern Regular expression used when filtering
     * @return FilterableInterface
     */
    public function filterName($pattern)
    {
        return $this->filter(new NameFilter($pattern));
    }

    /**
     * Create iterator where classes are filtered based on method return value
     *
     * @param  string  $methodName  Name of method
     * @param  mixed   $returnValue Expected return value
     * @return FilterableInterface
     */
    public function where($methodName, $returnValue = true)
    {
        return $this->filter(new WhereFilter($methodName, $returnValue));
    }

    /**
     * Negate a filter
     *
     * @param  FilterInterface $filter
     * @return FilterInterface
     */
    public function not(FilterInterface $filter)
    {
        return $this->filter(new NotFilter($filter));
    }

    /**
     * Cache iterator
     *
     * @return FilterableInterface
     */
    public function cache()
    {
        return $this->filter(new CacheFilter);
    }
}
