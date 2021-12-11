<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types = 1);

namespace hanneskod\classtools\Iterator;

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
class ClassIterator implements ClassIteratorInterface
{
    /**
     * @var SplFileInfo[] Maps names to SplFileInfo objects
     */
    private $classMap = [];

    /**
     * @var string[]
     */
    private $errors = [];

    /**
     * @var ClassLoader
     */
    private $loader;

    /**
     * Scan filesystem for classes, interfaces and traits
     */
    public function __construct(Finder $finder = null)
    {
        /** @var \Symfony\Component\Finder\SplFileInfo $fileInfo */
        foreach (($finder ?: []) as $fileInfo) {
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

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getClassMap(): array
    {
        return $this->classMap;
    }

    public function enableAutoloading(): void
    {
        $this->loader = new ClassLoader($this, true);
    }

    public function disableAutoloading(): void
    {
        if (isset($this->loader)) {
            $this->loader->unregister();
            unset($this->loader);
        }
    }

    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        /** @var SplFileInfo $fileInfo */
        foreach ($this->getClassMap() as $name => $fileInfo) {
            try {
                yield $name => new \ReflectionClass($name);
            } catch (\ReflectionException $e) {
                $msg = "Unable to iterate, {$e->getMessage()}, is autoloading enabled?";
                throw new LogicException($msg, 0, $e);
            }
        }
    }

    public function filter(Filter $filter): Filter
    {
        $filter->bindTo($this);
        return $filter;
    }

    public function type(string $typename): Filter
    {
        return $this->filter(new TypeFilter($typename));
    }

    public function name(string $pattern): Filter
    {
        return $this->filter(new NameFilter($pattern));
    }

    public function inNamespace(string $namespace): Filter
    {
        return $this->filter(new NamespaceFilter($namespace));
    }

    public function where(string $methodName, $expectedReturn = true): Filter
    {
        return $this->filter(new WhereFilter($methodName, $expectedReturn));
    }

    public function not(Filter $filter): Filter
    {
        return $this->filter(new NotFilter($filter));
    }

    public function cache(): Filter
    {
        return $this->filter(new CacheFilter);
    }

    public function transform(Writer $writer): string
    {
        $code = '';

        /** @var SplFileInfo $fileInfo */
        foreach ($this->getClassMap() as $name => $fileInfo) {
            $code .= $writer->write($fileInfo->getReader()->read($name)) . "\n";
        }

        return "<?php $code";
    }

    public function minimize(): string
    {
        return $this->transform(new MinimizingWriter);
    }
}
