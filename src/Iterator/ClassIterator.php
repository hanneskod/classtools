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
use ReflectionException;
use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Transformer\Reader;
use hanneskod\classtools\Transformer\Writer;
use hanneskod\classtools\Transformer\MinimizingWriter;
use hanneskod\classtools\Iterator\Filter\CacheFilter;
use hanneskod\classtools\Iterator\Filter\NameFilter;
use hanneskod\classtools\Iterator\Filter\NotFilter;
use hanneskod\classtools\Iterator\Filter\TypeFilter;
use hanneskod\classtools\Iterator\Filter\WhereFilter;
use hanneskod\classtools\Exception\LogicException;

/**
 * Iterate over classes found in filesystem
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ClassIterator implements IteratorAggregate
{
    /**
     * @var SplFileInfo[] Maps names to SplFileInfo objects
     */
    private $classMap = [];

    /**
     * Scan filesystem for classes, interfaces and traits
     *
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        /** @var \Symfony\Component\Finder\SplFileInfo $fileInfo */
        foreach ($finder as $fileInfo) {
            $fileInfo = new SplFileInfo($fileInfo);
            foreach ($fileInfo->getReader()->getDefinitionNames() as $name) {
                $this->classMap[$name] = $fileInfo;
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
        /** @var SplFileInfo $fileInfo */
        foreach ($this->getClassMap() as $name => $fileInfo) {
            try {
                yield $name => new ReflectionClass($name);
            } catch (ReflectionException $e) {
                $msg = "Unable to iterate, {$e->getMessage()}, use a ClassLoader to load classes from filesystem";
                throw new LogicException($msg, 0, $e);
            }
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

    /**
     * Transform found classes
     *
     * @param  Writer $writer
     * @return string
     */
    public function transform(Writer $writer)
    {
        $code = '';

        /** @var SplFileInfo $fileInfo */
        foreach ($this->getClassMap() as $name => $fileInfo) {
            $code .= $writer->write($fileInfo->getReader()->read($name)) . "\n";
        }

        return "<?php $code";
    }

    /**
     * Minimize found classes
     *
     * @return string
     */
    public function minimize()
    {
        return $this->transform(new MinimizingWriter);
    }
}
