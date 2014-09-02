<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Iterator\Filter;

use hanneskod\classtools\Iterator\ClassIterator;
use hanneskod\classtools\Iterator\Filter;
use hanneskod\classtools\Name;

/**
 * Filter classes based on namespace
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class NamespaceFilter extends ClassIterator implements Filter
{
    use FilterTrait;

    /**
     * @var Name Namespace to filter on
     */
    private $namespace;

    /**
     * Register namespace to filter on
     *
     * @param string $namespace
     */
    public function __construct($namespace)
    {
        $this->namespace = new Name((string)$namespace);
    }

    /**
     * Get iterator for definitions in namespace
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        foreach ($this->getBoundIterator() as $className => $reflectedClass) {
            if ((new Name($className))->inNamespace($this->namespace)) {
                yield $className => $reflectedClass;
            }
        }
    }
}
