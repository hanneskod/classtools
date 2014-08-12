<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Iterator;

use IteratorAggregate;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use hanneskod\classtools\Translator\Reader;
use hanneskod\classtools\Iterator\Filter\CacheFilter;
use hanneskod\classtools\Iterator\Filter\NameFilter;
use hanneskod\classtools\Iterator\Filter\NotFilter;
use hanneskod\classtools\Iterator\Filter\TypeFilter;
use hanneskod\classtools\Iterator\Filter\WhereFilter;

/**
 * Iterate over classes found in filesystem
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ClassIterator implements IteratorAggregate
{
    /**
     * @var SplFileInfo[] Maps names to filesystem paths
     */
    private $classMap = [];

    /**
     * Scan filesystem for classes, interfaces and traits
     *
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        /** @var SplFileInfo $fileinfo */
        foreach ($finder as $fileinfo) {
            $reader = new Reader($fileinfo->getContents());
            foreach ($reader->getDefinitionNames() as $name) {
                $this->classMap[$name] = $fileinfo;
            }
        }
    }

    /**
     * Get map of classnames to SplFileInfo objects
     *
     * @return SplFileInfo[]
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * Iterator yields classnames as keys and ReflectionClass objects as values
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        /** @var SplFileInfo $fileinfo */
        foreach ($this->getClassMap() as $name => $fileinfo) {
            if (!class_exists($name) && !interface_exists($name) && !trait_exists($name)) {
                // TODO fix needed!
                //include $fileinfo->getRealPath();
                eval(str_replace("<?php", "", $fileinfo->getContents()));
            }
            yield $name => new ReflectionClass($name);
        }
    }

    /**
     * Bind filter to iterator
     *
     * @param  Filter $filter
     * @return Filter The bound filter
     */
    public function filter(Filter $filter)
    {
        $filter->bindTo($this);
        return $filter;
    }

    /**
     * Create a new iterator where classes are filtered based on type
     *
     * @param  string $typename
     * @return ClassIterator
     */
    public function type($typename)
    {
        return $this->filter(new TypeFilter($typename));
    }

    /**
     * Create a new iterator where classes are filtered based on name
     *
     * @param  string $pattern Regular expression used when filtering
     * @return ClassIterator
     */
    public function name($pattern)
    {
        return $this->filter(new NameFilter($pattern));
    }

    /**
     * Create iterator where classes are filtered based on method return value
     *
     * @param  string  $methodName  Name of method
     * @param  mixed   $returnValue Expected return value
     * @return ClassIterator
     */
    public function where($methodName, $returnValue = true)
    {
        return $this->filter(new WhereFilter($methodName, $returnValue));
    }

    /**
     * Negate a filter
     *
     * @param  Filter $filter
     * @return Filter
     */
    public function not(Filter $filter)
    {
        return $this->filter(new NotFilter($filter));
    }

    /**
     * Cache iterator
     *
     * @return ClassIterator
     */
    public function cache()
    {
        return $this->filter(new CacheFilter);
    }
}
