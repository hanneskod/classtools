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
 * Filter classes based on name
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class NameFilter extends ClassIterator implements Filter
{
    use FilterTrait;

    /**
     * @var string Regular expression for matching definition names
     */
    private $pattern;

    /**
     * Register matching regular expression
     */
    public function __construct(string $pattern)
    {
        parent::__construct();
        $this->pattern = $pattern;
    }

    public function getIterator(): iterable
    {
        foreach ($this->getBoundIterator() as $className => $reflectedClass) {
            if (preg_match($this->pattern, $className)) {
                yield $className => $reflectedClass;
            }
        }
    }
}
