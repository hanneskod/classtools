<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools;

use hanneskod\classtools\Exception\RuntimeException;
use IteratorAggregate;
use ArrayIterator;

/**
 * Iterate over classes found in filesystem
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ClassIterator implements IteratorAggregate
{
    private $classes = array();

    /**
     * Add one or more paths for scanning
     *
     * @param array|string $paths
     */
    public function __construct($paths = null)
    {
        foreach ((array)$paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * Iterator yields classnames as keys and filesystem paths as values
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->classes);
    }

    /**
     * Add a path for scanning
     *
     * @param  string $path
     * @throws RuntimeException If $path is not a valid path
     */
    public function addPath($path)
    {
        if (is_dir($path)) {
            $this->addDir($path);
        } elseif (is_file($path)) {
            $this->addFile($path);
        } else {
            throw new RuntimeException("<$path> is not a valid filesystem path.");
        }
    }

    /**
     * @param string $dirname
     */
    private function addDir($dirname)
    {
        foreach (ClassMapGenerator::createMap($dirname) as $classname => $path) {
            $this->addClass($classname, $path);
        }
    }

    /**
     * @param string $filename
     */
    private function addFile($filename)
    {
        foreach (ClassMapGenerator::findClasses($filename) as $classname) {
            $this->addClass($classname, $filename);
        }
    }

    /**
     * Add class to iterator
     *
     * @param string $classname
     * @param mixed  $content
     */
    private function addClass($classname, $content = '')
    {
        $this->classes[$classname] = $content;
    }
}
