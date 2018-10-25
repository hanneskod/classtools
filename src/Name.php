<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

declare(strict_types = 1);

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
     */
    public function __construct(string $name)
    {
        $this->parts = explode('\\', $name);
    }

    /**
     * Get as string
     */
    public function __tostring(): string
    {
        return implode('\\', $this->parts);
    }

    /**
     * Get PhpParser node for this name
     */
    public function createNode(): PhpParserName
    {
        return new PhpParserName($this->parts);
    }

    /**
     * Checks if a class, interface, trait or function has been defined
     */
    public function isDefined(bool $autoload = true): bool
    {
        return class_exists((string)$this, $autoload)
            || interface_exists((string)$this, $autoload)
            || trait_exists((string)$this, $autoload)
            || function_exists((string)$this);
    }

    /**
     * Remove leading backslashes
     */
    public function normalize(): string
    {
        return preg_replace('/^\\\*/', '', (string)$this);
    }

    /**
     * Remove leading backslashes and convert case
     */
    public function keyize(): string
    {
        return strtolower($this->normalize());
    }

    /**
     * Get trailing name component
     */
    public function getBasename(): Name
    {
        return new Name((string)end($this->parts));
    }

    /**
     * Get parent namespace name component
     */
    public function getNamespace(): Name
    {
        $parts = $this->parts;
        array_pop($parts);
        return new Name(implode('\\', $parts));
    }

    /**
     * Check if name is in namespace
     */
    public function inNamespace(Name $namespace): bool
    {
        return !!preg_match(
            '/^'.preg_quote($namespace->keyize()).'/',
            $this->getNamespace()->keyize()
        );
    }
}
