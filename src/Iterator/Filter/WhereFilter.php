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
 * Filter classes based ReflectionClass method
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
final class WhereFilter extends ClassIterator implements Filter
{
    use FilterTrait;

    /**
     * @var string Name of ReflectionClass method to evaluate
     */
    private $methodName;

    /**
     * @var mixed Expected return value
     */
    private $returnValue;

    /**
     * Register method name and expected return value
     *
     * @param string $methodName  Name of ReflectionClass method to evaluate
     * @param mixed  $returnValue Expected return value
     */
    public function __construct($methodName, $returnValue = true)
    {
        parent::__construct();
        $this->methodName = $methodName;
        $this->returnValue = $returnValue;
    }

    public function getIterator(): iterable
    {
        $methodName = $this->methodName;
        foreach ($this->getBoundIterator() as $className => $reflectedClass) {
            if ($reflectedClass->$methodName() == $this->returnValue) {
                yield $className => $reflectedClass;
            }
        }
    }
}
