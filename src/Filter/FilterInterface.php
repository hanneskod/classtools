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
use hanneskod\classtools\Exception\LogicException;

/**
 * Defines a FilterableClassIterator filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface FilterInterface
{
    /**
     * Bind filter to iterator
     *
     * @param FilterableClassIterator $iterator
     */
    public function bindTo(FilterableClassIterator $iterator);

    /**
     * Get iterator bound to filter
     *
     * @return FilterableClassIterator
     * @throws LogicException If no bound iterator exists
     */
    public function getBoundIterator();
}
