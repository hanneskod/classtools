<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Extractor;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\PrettyPrinterAbstract;
use PhpParser\Error as PhpParserException;
use hanneskod\classtools\Exception\RuntimeException;

/**
 * Wrapper for parsed code snippets
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class CodeObject
{
    /**
     * @var array Parsed code
     */
    private $statements;

    /**
     * @var NodeTraverser Traverser used for altering parsed code
     */
    private $traverser;

    /**
     * @var PrettyPrinterAbstract Printer used for printing traversed code
     */
    private $printer;

    /**
     * Optionally inject dependencies
     *
     * @param array         $statements
     * @param NodeTraverser $traverser
     */
    public function __construct(array $statements, NodeTraverser $traverser = null)
    {
        $this->statements = $statements;
        $this->traverser = $traverser ?: new NodeTraverser;
    }

    /**
     * Register visitor to alter code
     *
     * @param  NodeVisitor $visitor
     * @return CodeObject  Instance for chaining
     */
    public function registerVisitor(NodeVisitor $visitor)
    {
        $this->traverser->addVisitor($visitor);
        return $this;
    }

    /**
     * Set statement printer
     *
     * @param  PrettyPrinterAbstract $printer
     * @return CodeObject  Instance for chaining
     */
    public function setPrinter(PrettyPrinterAbstract $printer)
    {
        $this->printer = $printer;
        return $this;
    }

    /**
     * Get registered printer
     *
     * @return PrettyPrinterAbstract
     */
    public function getPrinter()
    {
        return $this->printer ?: new BracketingPrinter;
    }

    /**
     * Generate new code snippet
     *
     * @return string
     * @throws RuntimeException If code generation failes
     */
    public function getCode()
    {
        try {
            return $this->getPrinter()->prettyPrint(
                $this->traverser->traverse(
                    $this->statements
                )
            );
        } catch (PhpParserException $e) {
            throw new RuntimeException("Error generating code: {$e->getMessage()}", 0, $e);
        }
    }
}
