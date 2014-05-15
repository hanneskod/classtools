<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools;

use IteratorAggregate;
use hanneskod\classtools\Filter\FilterInterface;

/**
 * Defines a filterable iterator
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface FilterableInterface extends IteratorAggregate
{
    /**
     * Bind filter to filterable
     *
     * @param  FilterInterface $filter
     * @return FilterInterface The bound filter
     */
    public function filter(FilterInterface $filter);

    /**
     * Create a new iterator where classes are filtered based on type
     *
     * @param  string $typename
     * @return FilterableInterface
     */
    public function filterType($typename);

    /**
     * Create a new iterator where classes are filtered based on name
     *
     * @param  string $pattern Regular expression used when filtering
     * @return FilterableInterface
     */
    public function filterName($pattern);

    /**
     * Create iterator where classes are filtered based on method return value
     *
     * @param  string  $methodName  Name of method
     * @param  mixed   $returnValue Expected return value
     * @return FilterableInterface
     */
    public function where($methodName, $returnValue = true);

    /**
     * Negate a filter
     *
     * @param  FilterInterface $filter
     * @return FilterInterface
     */
    public function not(FilterInterface $filter);

    /**
     * Cache iterator
     *
     * @return FilterableInterface
     */
    public function cache();
}
