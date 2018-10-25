<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools;

use PhpParser\Node\Name as PhpParserName;

/**
 * Internal name representation
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Name
{
    /**
     * @var string[] Name components
     */
    private $parts;

    /**
     * Set name at construct
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->parts = explode('\\', $name);
    }

    /**
     * Get as string
     *
     * @return string
     */
    public function __tostring()
    {
        return implode('\\', $this->parts);
    }

    /**
     * Get PhpParser node for this name
     *
     * @return PhpParserName
     */
    public function createNode()
    {
        return new PhpParserName($this->parts);
    }

    /**
     * Checks if a class, interface, trait or function has been defined
     *
     * @param  boolean $autoload Whether to call __autoload or not by default
     * @return boolean
     */
    public function isDefined($autoload = true)
    {
        return class_exists((string)$this, $autoload)
            || interface_exists((string)$this, $autoload)
            || trait_exists((string)$this, $autoload)
            || function_exists((string)$this);
    }

    /**
     * Remove leading backslashes
     *
     * @return string
     */
    public function normalize()
    {
        return preg_replace('/^\\\*/', '', (string)$this);
    }

    /**
     * Remove leading backslashes and convert case
     *
     * @return string
     */
    public function keyize()
    {
        return strtolower($this->normalize());
    }

    /**
     * Get trailing name component
     *
     * @return Name
     */
    public function getBasename()
    {
        return new Name((string)end($this->parts));
    }

    /**
     * Get parent namespace name component
     *
     * @return Name
     */
    public function getNamespace()
    {
        $parts = $this->parts;
        array_pop($parts);
        return new Name(implode('\\', $parts));
    }

    /**
     * Check if name is in namespace
     *
     * @param  Name $namespace
     * @return bool
     */
    public function inNamespace(Name $namespace)
    {
        return !!preg_match(
            '/^'.preg_quote($namespace->keyize()).'/',
            $this->getNamespace()->keyize()
        );
    }
}
