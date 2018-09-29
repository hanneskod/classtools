<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types = 1);

namespace hanneskod\classtools\Instantiator;

use hanneskod\classtools\Exception\LogicException;

/**
 * Instantiate reflected class
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Instantiator
{
    /**
     * @var \ReflectionClass
     */
    private $class;

    /**
     * Set class to instantiate
     */
    public function setReflectionClass(\ReflectionClass $class): void
    {
        $this->class = $class;
    }

    /**
     * Get loaded reflection class
     *
     * @throws LogicException  If reflected class is not loaded
     */
    public function getReflectionClass(): \ReflectionClass
    {
        if (!isset($this->class)) {
            throw new LogicException("Reflected class not loaded");
        }
        return $this->class;
    }

    /**
     * Get number of required constructor parameters
     */
    public function countConstructorArgs(): int
    {
        if ($constructor = $this->class->getConstructor()) {
            return $constructor->getNumberOfRequiredParameters();
        }
        return 0;
    }

    /**
     * Check if class is instantiable
     */
    public function isInstantiable(): bool
    {
        return $this->getReflectionClass()->isInstantiable();
    }

    /**
     * Check if class is instantiable without constructor parameters
     */
    public function isInstantiableWithoutArgs(): bool
    {
        return $this->isInstantiable() && !$this->countConstructorArgs();
    }

    /**
     * Create instance
     *
     * @param  array          $args Optional constructor arguments
     * @return mixed          Instance of reflected class
     * @throws LogicException If reflected class is not instantiable
     */
    public function instantiate(array $args = array())
    {
        if (!$this->isInstantiable()) {
            throw new LogicException("Reflected class is not instantiable");
        }

        if (count($args) < $this->countConstructorArgs()) {
            throw new LogicException("Unable to instantiate, too few constructor arguments");
        }

        return $this->getReflectionClass()->newInstanceArgs($args);
    }
}
