<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Extractor;

use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use hanneskod\classtools\Exception\RuntimeException;

/**
 * Extract classes, interfaces and traits from php snippets
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Extractor
{
    /**
     * @var array Collection of definitions in snippet
     */
    private $defs = array();

    /**
     * Optionally inject parser
     *
     * @param string $snippet
     * @param Parser $parser
     */
    public function __construct($snippet, Parser $parser = null)
    {
        $parser = $parser ?: new Parser(new Lexer);
        $this->parseDefinitions(
            $parser->parse($snippet)
        );
    }

    /**
     * Find class, interface and trait definitions in statemnts
     *
     * @param  array      $stmts
     * @param  Namespace_ $namespace
     * @return void
     */
    private function parseDefinitions(array $stmts, Namespace_ $namespace = null)
    {
        // Save the global statments
        $this->storeDefinition('0', $stmts);

        foreach ($stmts as $stmt) {
            if ($stmt instanceof Namespace_) {
                $this->parseDefinitions(
                    $stmt->stmts,
                    $stmt
                );
            } elseif ($stmt instanceof Class_ or $stmt instanceof Interface_ or $stmt instanceof Trait_) {
                if (!$namespace) {
                    $this->storeDefinition($stmt->name, $stmt);
                } else {
                    $namespace->stmts = array($stmt);
                    $this->storeDefinition($namespace->name . "\\" . $stmt->name, $namespace);
                }
            }
        }
    }

    /**
     * Store found definition
     *
     * @param  string       $name
     * @param  array|object $def
     * @return void
     */
    private function storeDefinition($name, $def)
    {
        if (!is_array($def)) {
            $def = array($def);
        }
        $this->defs[strtolower($name)] = $def;
    }

    /**
     * Get names of definitions in snippet
     *
     * @return string[]
     */
    public function getDefinitionNames()
    {
        return array_keys($this->defs);
    }

    /**
     * Check if snippet contains definition
     *
     * @param  string  $name Fully qualified name
     * @return boolean
     */
    public function hasDefinition($name)
    {
        return isset($this->defs[strtolower($name)]);
    }

    /**
     * Get code for class/interface/trait
     *
     * @param  string $name Name of class/interface/trait
     * @return CodeObject
     * @throws RuntimeException If $name does not exist
     */
    public function extract($name)
    {
        if (!$this->hasDefinition($name)) {
            throw new RuntimeException("Unable to extract <$name>, not found.");
        }

        return new CodeObject($this->defs[strtolower($name)]);
    }

    /**
     * Get global code
     *
     * @return CodeObject
     */
    public function extractGlobal()
    {
        return $this->extract('0');
    }
}
