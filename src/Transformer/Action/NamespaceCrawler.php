<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Transformer\Action;

use hanneskod\classtools\Name;
use hanneskod\classtools\Exception\RuntimeException;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;

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
     * @var Name[] List of namespaces to test
     */
    private $search = [];

    /**
     * @var Name[] List of namespaces to ignore when crawling
     */
    private $ignore = [];

    /**
     * @var boolean Whether exceptions should be thrown when a name can not be resolved
     */
    private $throw;

    /**
     * Search namespaces for definied classes
     *
     * @param string[] $search List of namespaces to test
     * @param string[] $ignore List of namespaces to ignore when crawling
     * @param boolean  $throw  Flag if exceptions should be thrown when a name can not be resolved
     */
    public function __construct(array $search, array $ignore = array(), $throw = true)
    {
        foreach ($search as $namespace) {
            $this->search[] = new Name((string)$namespace);
        }

        foreach ($ignore as $namespace) {
            $this->ignore[] = new Name((string)$namespace);
        }

        $this->throw = $throw;
    }

    /**
     * Resolve unexisting names by searching specified namespaces
     *
     * @param  Node $node
     * @return FullyQualified|null
     * @throws RuntimeException If name can not be resolved
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof FullyQualified) {
            $name = new Name((string)$node);
            if (!$this->isResolved($name)) {
                /** @var Name $namespace */
                foreach ($this->search as $namespace) {
                    $newName = new Name("{$namespace}\\{$node->getLast()}");
                    if ($this->isResolved($newName)) {
                        return $newName->createNode();
                    }
                }
                if ($this->throw) {
                    throw new RuntimeException("Unable to resolve class <$node>.");
                }
            }
        }
    }

    /**
     * Check if name is resolved
     *
     * @param  Name    $name
     * @return boolean
     */
    public function isResolved(Name $name)
    {
        /** @var Name $ignore */
        foreach ($this->ignore as $ignore) {
            if ($name->inNamespace($ignore)) {
                return true;
            }
        }
        return $name->isDefined();
    }
}
