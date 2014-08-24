<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Transformer;

use PhpParser\Parser;
use PhpParser\Lexer\Emulative;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use hanneskod\classtools\Exception\RuntimeException;

/**
 * Read classes, interfaces and traits from php snippets
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Reader
{
    /**
     * @var array Collection of definitions in snippet
     */
    private $defs = [];

    /**
     * @var string[] Case sensitive definition names
     */
    private $names = [];

    /**
     * @var array The global statement object
     */
    private $global;

    /**
     * Optionally inject parser
     *
     * @param string $snippet
     * @param Parser $parser
     */
    public function __construct($snippet, Parser $parser = null)
    {
        $parser = $parser ?: new Parser(new Emulative);

        // Save the global statments
        $this->global = $parser->parse($snippet);

        $this->findDefinitions(
            $this->global,
            new Namespace_(new Name([]), [])
        );
    }

    /**
     * Find class, interface and trait definitions in statemnts
     *
     * @param  array      $stmts
     * @param  Namespace_ $namespace
     * @return void
     */
    private function findDefinitions(array $stmts, Namespace_ $namespace)
    {
        foreach ($stmts as $stmt) {
            // Restart if namespace declaration is found
            if ($stmt instanceof Namespace_) {
                $this->findDefinitions(
                    $stmt->stmts,
                    new Namespace_($stmt->name, [])
                );

            // Save use declaration to namespace
            } elseif ($stmt instanceof Use_) {
                $namespace->stmts[] = $stmt;

            // Save classes, interfaces and traits
            } elseif ($stmt instanceof Class_ or $stmt instanceof Interface_ or $stmt instanceof Trait_) {
                $namespace->stmts[] = $stmt;
                $name = self::normalizeName($namespace->name . "\\" . $stmt->name);
                $key = self::getKey($name);
                $this->defs[$key] = [clone $namespace];
                $this->names[$key] = $name;
            }
        }
    }

    /**
     * Normalize definition name
     *
     * @param  string $name
     * @return string
     */
    private static function normalizeName($name)
    {
        return preg_replace('/^\\\*/', '', $name);
    }

    /**
     * Get key for definition name
     *
     * @param  string $name
     * @return string
     */
    private static function getKey($name)
    {
        return strtolower(self::normalizeName($name));
    }

    /**
     * Get names of definitions in snippet
     *
     * @return string[]
     */
    public function getDefinitionNames()
    {
        return array_values($this->names);
    }

    /**
     * Check if snippet contains definition
     *
     * @param  string  $name Fully qualified name
     * @return boolean
     */
    public function hasDefinition($name)
    {
        return isset($this->defs[self::getKey($name)]);
    }

    /**
     * Get pare tree for class/interface/trait
     *
     * @param  string $name Name of class/interface/trait
     * @return array
     * @throws RuntimeException If $name does not exist
     */
    public function read($name)
    {
        if (!$this->hasDefinition($name)) {
            throw new RuntimeException("Unable to read <$name>, not found.");
        }

        return $this->defs[self::getKey($name)];
    }

    /**
     * Get parse tree for the complete snippet
     *
     * @return array
     */
    public function readAll()
    {
        return $this->global;
    }
}
