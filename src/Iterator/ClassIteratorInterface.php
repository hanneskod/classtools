<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types = 1);

namespace hanneskod\classtools\Iterator;

use hanneskod\classtools\Transformer\Writer;

/**
 * Iterate over classes found in filesystem
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface ClassIteratorInterface extends \IteratorAggregate
{
    /**
     * Get syntax errors encountered in source
     *
     * @return string[]
     */
    public function getErrors(): array;

    /**
     * Get map of classnames to SplFileInfo objects
     *
     * @return SplFileInfo[]
     */
    public function getClassMap(): array;

    /**
     * Enable autoloading for classes found in filesystem
     */
    public function enableAutoloading(): void;

    /**
     * Disable autoloading for classes found in filesystem
     */
    public function disableAutoloading(): void;

    /**
     * Iterator yields classnames as keys and ReflectionClass objects as values
     */
    #[\ReturnTypeWillChange]
    public function getIterator();

    /**
     * Filter this iterator
     */
    public function filter(Filter $filter): Filter;

    /**
     * Create a new iterator where classes are filtered based on type
     */
    public function type(string $typename): Filter;

    /**
     * Create a new iterator where classes are filtered based on name
     */
    public function name(string $pattern): Filter;

    /**
     * Create a new iterator where classes are filtered based on namespace
     */
    public function inNamespace(string $namespace): Filter;

    /**
     * Create iterator where classes are filtered based on method return value
     */
    public function where(string $methodName, $expectedReturn = true): Filter;

    /**
     * Register a negated filter
     */
    public function not(Filter $filter): Filter;

    /**
     * Cache iterator
     */
    public function cache(): Filter;

    /**
     * Transform found classes
     */
    public function transform(Writer $writer): string;

    /**
     * Minimize found classes
     */
    public function minimize(): string;
}
