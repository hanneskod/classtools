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
use hanneskod\classtools\Exception\RuntimeException;
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
    private $classes = [];

    /**
     * Add one or more paths for scanning
     *
     * @param array|string $paths
     */
    public function __construct($paths = null)
    {
        foreach ((array)$paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * Add a path for scanning
     *
     * @param  string $path
     * @throws RuntimeException If $path is not a valid path
     */
    public function addPath($path)
    {
        if (is_dir($path)) {
            $this->addDir($path);
        } elseif (is_file($path)) {
            $this->addFile($path);
        } else {
            throw new RuntimeException("<$path> is not a valid filesystem path.");
        }
    }

    /**
     * @param string $dirname
     */
    private function addDir($dirname)
    {
        foreach (ClassMapGenerator::createMap($dirname) as $classname => $path) {
            $this->addClass($classname, $path);
        }
    }

    /**
     * @param string $filename
     */
    private function addFile($filename)
    {
        foreach (ClassMapGenerator::findClasses($filename) as $classname) {
            $this->addClass($classname, $filename);
        }
    }

    /**
     * Add class to iterator
     *
     * @param string $classname
     * @param mixed  $content
     */
    private function addClass($classname, $content = '')
    {
        $this->classes[$classname] = $content;
    }

    /**
     * Get map of classnames to filesystem paths
     *
     * @return array
     */
    public function getClassMap()
    {
        return $this->classes;
    }

    /**
     * Iterator yields classnames as keys and ReflectionClass objects as values
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        foreach ($this->getClassMap() as $className => $path) {
            yield $className => new ReflectionClass($className);
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
    public function filterType($typename)
    {
        return $this->filter(new TypeFilter($typename));
    }

    /**
     * Create a new iterator where classes are filtered based on name
     *
     * @param  string $pattern Regular expression used when filtering
     * @return ClassIterator
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
