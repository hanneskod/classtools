<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Iterator;

use IteratorAggregate;
use hanneskod\classtools\Exception\LogicException;

/**
 * Definition of a ClassIterator filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface Filter extends IteratorAggregate
{
    /**
     * Bind filter to iterator
     *
     * @param  ClassIterator $iterator
     * @return void
     */
    public function bindTo(ClassIterator $iterator);

    /**
     * Get iterator bound to filter
     *
     * @return ClassIterator
     * @throws LogicException If no bound iterator exists
     */
    public function getBoundIterator();
}
