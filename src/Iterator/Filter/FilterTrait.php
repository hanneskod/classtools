<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types = 1);

namespace hanneskod\classtools\Iterator\Filter;

use hanneskod\classtools\Iterator\ClassIteratorInterface;
use hanneskod\classtools\Exception\LogicException;
use hanneskod\classtools\Iterator\SplFileInfo;

/**
 * Implementation of Filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
trait FilterTrait
{
    /**
     * @var ClassIteratorInterface Iterator filter is bound to
     */
    private $boundIterator;

    public function bindTo(ClassIteratorInterface $iterator): void
    {
        $this->boundIterator = $iterator;
    }

    public function getBoundIterator(): ClassIteratorInterface
    {
        if (!isset($this->boundIterator)) {
            throw new LogicException("Filter not bound to iterator.");
        }
        return $this->boundIterator;
    }

    /**
     * Get map of classnames to SplFileInfo objects
     *
     * @return SplFileInfo[]
     */
    public function getClassMap(): array
    {
        $parentMap = $this->getBoundIterator()->getClassMap();
        $map = iterator_to_array($this->getIterator());

        foreach ($map as $name => &$fileinfo) {
            $fileinfo = $parentMap[$name];
        }

        return $map;
    }

    /**
     * Get current iterator
     */
    abstract public function getIterator(): iterable;
}
