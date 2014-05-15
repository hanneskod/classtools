<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Filter;

use hanneskod\classtools\FilterableInterface;
use hanneskod\classtools\Exception\LogicException;

/**
 * Implementation of FilterInterface
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
trait FilterInterfaceTrait
{
    private $iterator;

    /**
     * Bind filter to iterator
     *
     * @param  FilterableInterface $iterator
     * @return void
     */
    public function bindTo(FilterableInterface $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * Get iterator bound to filter
     *
     * @return FilterableInterface
     * @throws LogicException If no bound iterator exists
     */
    public function getBoundIterator()
    {
        if (!isset($this->iterator)) {
            throw new LogicException("Filter not bound to iterator.");
        }
        return $this->iterator;
    }
}
