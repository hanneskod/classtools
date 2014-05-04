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
 * Filter classes based on name
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class NameFilter extends FilterableClassIterator implements FilterInterface
{
    use FilterInterfaceTrait;

    private $pattern;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    public function getIterator()
    {
        foreach ($this->getBoundIterator() as $className => $reflectedClass) {
            if (preg_match($this->pattern, $className)) {
                yield $className => $reflectedClass;
            }
        }
    }
}
