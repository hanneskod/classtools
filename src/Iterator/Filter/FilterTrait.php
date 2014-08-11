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
use hanneskod\classtools\Exception\LogicException;

/**
 * Implementation of Filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
trait FilterTrait
{
    private $boundIterator;

    /**
     * Bind filter to iterator
     *
     * @param  ClassIterator $iterator
     * @return void
     */
    public function bindTo(ClassIterator $iterator)
    {
        $this->boundIterator = $iterator;
    }

    /**
     * Get iterator bound to filter
     *
     * @return ClassIterator
     * @throws LogicException If no bound iterator exists
     */
    public function getBoundIterator()
    {
        if (!isset($this->boundIterator)) {
            throw new LogicException("Filter not bound to iterator.");
        }
        return $this->boundIterator;
    }

    /**
     * Get iterator that yields classnames as keys and filesystem paths as values
     *
     * @return \Iterator
     */
    public function getClassMap()
    {
        foreach ($this->getIterator() as $className => $reflectedClass) {
            yield $className => $reflectedClass->getFileName();
        }
    }
}
