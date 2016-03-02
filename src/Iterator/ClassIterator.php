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
use hanneskod\classtools\Transformer\Writer;
use hanneskod\classtools\Transformer\MinimizingWriter;
use hanneskod\classtools\Iterator\Filter\CacheFilter;
use hanneskod\classtools\Iterator\Filter\NameFilter;
use hanneskod\classtools\Iterator\Filter\NamespaceFilter;
use hanneskod\classtools\Iterator\Filter\NotFilter;
use hanneskod\classtools\Iterator\Filter\TypeFilter;
use hanneskod\classtools\Iterator\Filter\WhereFilter;
use hanneskod\classtools\Exception\LogicException;
use hanneskod\classtools\Loader\ClassLoader;
use hanneskod\classtools\Exception\ReaderException;

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
     * @var string[] List of reader error messages
     */
    private $errors = [];

    /**
     * @var ClassLoader Autoloader for found classes
     */
    private $loader;

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
            try {
                foreach ($fileInfo->getReader()->getDefinitionNames() as $name) {
                    $this->classMap[$name] = $fileInfo;
                }
            } catch (ReaderException $exception) {
                $this->errors[] = $exception->getMessage();
            }
        }
    }

    /**
     * Enable garbage collection of the autoloader at destruct
     */
    public function __destruct()
    {
        $this->disableAutoloading();
    }

    /**
     * Get syntax errors encountered in source
     *
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
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
     * Enable autoloading for classes found in filesystem
     *
     * @return ClassIterator instance for chaining
     */
    public function enableAutoloading()
    {
        $this->loader = new ClassLoader($this, true);
        return $this;
    }

    /**
     * Disable autoloading for classes found in filesystem
     *
     * @return ClassIterator instance for chaining
     */
    public function disableAutoloading()
    {
        if (isset($this->loader)) {
            $this->loader->unregister();
            unset($this->loader);
        }
        return $this;
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
                $msg = "Unable to iterate, {$e->getMessage()}, is autoloading enabled?";
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
     * @return Filter The created filter
     */
    public function type($typename)
    {
        return $this->filter(new TypeFilter($typename));
    }

    /**
     * Create a new iterator where classes are filtered based on name
     *
     * @param  string $pattern Regular expression used when filtering
     * @return Filter The created filter
     */
    public function name($pattern)
    {
        return $this->filter(new NameFilter($pattern));
    }

    /**
     * Create a new iterator where classes are filtered based on namespace
     *
     * @param  string $namespace Namespace used when filtering
     * @return Filter The created filter
     */
    public function inNamespace($namespace)
    {
        return $this->filter(new NamespaceFilter($namespace));
    }

    /**
     * Create iterator where classes are filtered based on method return value
     *
     * @param  string $methodName  Name of method
     * @param  mixed  $returnValue Expected return value
     * @return Filter The created filter
     */
    public function where($methodName, $returnValue = true)
    {
        return $this->filter(new WhereFilter($methodName, $returnValue));
    }

    /**
     * Negate a filter
     *
     * @param  Filter $filter
     * @return Filter The created filter
     */
    public function not(Filter $filter)
    {
        return $this->filter(new NotFilter($filter));
    }

    /**
     * Cache iterator
     *
     * @return Filter The created filter
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
