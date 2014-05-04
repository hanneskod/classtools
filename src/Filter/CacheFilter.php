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
use ArrayIterator;

/**
 * Cache bound iterator
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class CacheFilter extends FilterableClassIterator implements FilterInterface
{
    use FilterInterfaceTrait;

    private $cache;

    public function __construct()
    {
    }

    public function getIterator()
    {
        if (!isset($this->cache)) {
            $this->cache = new ArrayIterator(iterator_to_array($this->getBoundIterator()));
        }

        return $this->cache;
    }
}
