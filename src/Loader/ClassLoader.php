<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types = 1);

namespace hanneskod\classtools\Loader;

use hanneskod\classtools\Iterator\ClassIterator;

/**
 * Autoload classes from a ClassIterator classmap
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ClassLoader
{
    /**
     * @var \hanneskod\classtools\Iterator\SplFileInfo[] Maps names to SplFileInfo objects
     */
    private $classMap = [];

    /**
     * Load classmap at construct
     */
    public function __construct(ClassIterator $classIterator, bool $register = true)
    {
        $this->classMap = $classIterator->getClassMap();
        if ($register) {
            $this->register();
        }
    }

    /**
     * Register autoloader
     */
    public function register(): bool
    {
        return spl_autoload_register([$this, 'load']);
    }

    /**
     * Unregister autoloader
     */
    public function unregister(): bool
    {
        return spl_autoload_unregister([$this, 'load']);
    }

    /**
     * Attempt to load class definition
     */
    public function load(string $classname): void
    {
        if (isset($this->classMap[$classname])) {
            require $this->classMap[$classname]->getRealPath();
        }
    }
}
