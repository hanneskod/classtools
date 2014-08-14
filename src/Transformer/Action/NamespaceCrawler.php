<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Transformer\Action;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use hanneskod\classtools\Exception\RuntimeException;

/**
 * Search namespaces for definied classes
 *
 * Crawl namespaces for classes that were wrongly put in namespace by NameResolver
 * (if code is moved to the namespace before parsing takes place).
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class NamespaceCrawler extends NodeVisitorAbstract
{
    /**
     * @var string[] List of namespaces to crawl
     */
    private $namespaces;

    /**
     * @var boolean Whether exceptions should be thrown when a name can not be resolved
     */
    private $throw;

    /**
     * Search namespaces for definied classes
     *
     * @param string[] $namespaces List of namespaces to crawl
     * @param boolean  $throw      Flag if exceptions should be thrown when a name can not be resolved
     */
    public function __construct(array $namespaces, $throw = true)
    {
        $this->namespaces = $namespaces;
        $this->throw = $throw;
    }

    /**
     * {inheritdoc}
     *
     * @param  Node $node
     * @return FullyQualified|null
     * @throws RuntimeException If name can not be resolved
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof FullyQualified) {
            $className = $node->toString();

            if (!class_exists($className)) {
                foreach ($this->namespaces as $namespace) {
                    $newName = new FullyQualified($namespace.'\\'.$node->getLast());
                    if (class_exists($newName->toString())) {
                        return $newName;
                    }
                }
                if ($this->throw) {
                    throw new RuntimeException("Unable to resolve class <$className>.");
                }
            }
        }
    }
}
