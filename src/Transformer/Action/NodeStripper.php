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

/**
 * Strip nodes of registered type
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class NodeStripper extends NodeVisitorAbstract
{
    /**
     * @var string Name of node to strip
     */
    private $nodeType;

    /**
     * Register what nodes to strip using a fully quilified PhpParser class name
     *
     * @param string $nodeType Node type (see Node::getType())
     */
    public function __construct($nodeType)
    {
        $this->nodeType = $nodeType;
    }

    /**
     * Perform action
     *
     * @param  Node       $node
     * @return false|null Null if node should remain, false if not
     */
    public function leaveNode(Node $node)
    {
        if ($node->getType() == $this->nodeType) {
            return false;
        }
    }
}
