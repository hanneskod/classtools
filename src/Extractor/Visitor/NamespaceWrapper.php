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
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Name;

/**
 * Wrap code in namespace
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class NamespaceWrapper extends NodeVisitorAbstract
{
    /**
     * @var string Name of namespace
     */
    private $namespace;

    /**
     * Wrap code in namespace
     *
     * @param string $namespace Name of namespace
     */
    public function __construct($namespace = '')
    {
        $this->namespace = $namespace;
    }

    /**
     * {inheritdoc}
     *
     * @param  array $nodes
     * @return Namespace_[]
     */
    public function beforeTraverse(array $nodes)
    {
        // Prepend namespace if code is namespaced
        if ($nodes[0] instanceof Namespace_) {
            if ($this->namespace) {
                $nodes[0]->name->prepend($this->namespace);
            }
            return $nodes;
        }

        // Else create new node
        return array(
            new Namespace_(
                new Name($this->namespace),
                $nodes
            )
        );
    }
}
