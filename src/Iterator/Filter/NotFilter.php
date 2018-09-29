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

use hanneskod\classtools\Iterator\ClassIterator;
use hanneskod\classtools\Iterator\Filter;

/**
 * Negate a filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class NotFilter extends ClassIterator implements Filter
{
    use FilterTrait;

    /**
     * @var Filter
     */
    private $filter;

    public function __construct(Filter $filter)
    {
        parent::__construct();
        $this->filter = $filter;
    }

    public function getIterator(): iterable
    {
        $filtered = iterator_to_array($this->filter->getIterator());
        foreach ($this->getBoundIterator() as $className => $reflectedClass) {
            if (!isset($filtered[$className])) {
                yield $className => $reflectedClass;
            }
        }
    }
}
