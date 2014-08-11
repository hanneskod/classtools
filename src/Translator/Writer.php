<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Translator;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\PrettyPrinterAbstract;
use PhpParser\Error as PhpParserException;
use hanneskod\classtools\Exception\RuntimeException;

/**
 * Translate and print parsed code snippets
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Writer
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
     * Apply translation to alter code
     *
     * @param  NodeVisitor $translation
     * @return Writer      Instance for chaining
     */
    public function apply(NodeVisitor $translation)
    {
        $this->traverser->addVisitor($translation);
        return $this;
    }

    /**
     * Set statement printer
     *
     * @param  PrettyPrinterAbstract $printer
     * @return Writer Instance for chaining
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
    public function write()
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
