<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Iterator;

/**
 * Autoload classes from a ClassIterator classmap
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ClassLoader
{
    /**
     * @var SplFileInfo[] Maps names to SplFileInfo objects
     */
    private $classMap = [];

    /**
     * Load classmap at construct
     *
     * @param SplFileInfo[] $classMap
     */
    public function __construct(array $classMap)
    {
        $this->classMap = $classMap;
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
     * @param  string $class
     * @return null
     */
    public function load($class)
    {
        if (isset($this->classMap[$class])) {
            require $this->classMap[$class]->getRealPath();
        }
    }
}
