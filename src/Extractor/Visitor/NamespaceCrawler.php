<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Extractor\Visitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use hanneskod\classtools\Exception\RuntimeException;

/**
 * Search multiple namespaces for definied classes
 *
 * Crawl namespaces for classes that were wrongly put in namespace by
 * PhpParser\NodeVisitor\NameResolver (if code is moved to the namespace before
 * parsing takes place).
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class NamespaceCrawler extends NodeVisitorAbstract
{
    /**
     * @var array List of namesaces to crawl
     */
    private $namespaces;

    /**
     * Search multiple namespaces for definied classes
     *
     * @param array $namespaces
     */
    public function __construct(array $namespaces)
    {
        $this->namespaces = $namespaces;
    }

    /**
     * {inheritdoc}
     *
     * @param  Node $node
     * @return FullyQualified|null
     * @throws RuntimeException If name can not be resolved
     */
    public function leaveNode(Node $node) {
        if ($node instanceof FullyQualified) {
            $className = $node->toString();

            if (!class_exists($className)) {
                foreach ($this->namespaces as $namespace) {
                    $newName = new FullyQualified($namespace.'\\'.$node->getLast());
                    if (class_exists($newName->toString())) {
                        return $newName;
                    }
                }
                throw new RuntimeException("Unable to resolve class <$className>.");
            }
        }
    }
}
