<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Filter;

use hanneskod\classtools\FilterableClassIterator;

/**
 * Negate a filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class NotFilter extends FilterableClassIterator implements FilterInterface
{
    use FilterInterfaceTrait;

    private $filter;

    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    public function getIterator()
    {
        $filtered = iterator_to_array($this->filter->getIterator());
        foreach ($this->getBoundIterator() as $className => $reflectedClass) {
            if (!isset($filtered[$className])) {
                yield $className => $reflectedClass;
            }
        }
    }
}
