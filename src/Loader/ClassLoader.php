<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

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
     *
     * @param ClassIterator $classIterator
     * @param boolean       $register      True if loader should be register at creation
     */
    public function __construct(ClassIterator $classIterator, $register = true)
    {
        $this->classMap = $classIterator->getClassMap();
        if ($register) {
            $this->register();
        }
    }

    /**
     * Register autoloader
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     */
    public function register()
    {
        return spl_autoload_register([$this, 'load']);
    }

    /**
     * Unregister autoloader
     *
     * @return boolean Returns TRUE on success or FALSE on failure
     */
    public function unregister()
    {
        return spl_autoload_unregister([$this, 'load']);
    }

    /**
     * Attempt to load class definition
     *
     * @param  string $classname
     * @return null
     */
    public function load($classname)
    {
        if (isset($this->classMap[$classname])) {
            require $this->classMap[$classname]->getRealPath();
        }
    }
}
