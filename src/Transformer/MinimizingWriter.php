<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Transformer;

use PhpParser\NodeTraverser;
use hanneskod\classtools\Transformer\Action\CommentStripper;
use hanneskod\classtools\Transformer\Action\NodeStripper;
use hanneskod\classtools\Transformer\Action\NameResolver;

/**
 * Minimize parsed code snippets
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class MinimizingWriter extends Writer
{
    /**
     * Load minimizing translations at construct
     *
     * @param NodeTraverser $traverser
     */
    public function __construct(NodeTraverser $traverser = null)
    {
        parent::__construct($traverser);
        $this->apply(new CommentStripper);
        $this->apply(new NameResolver);
        $this->apply(new NodeStripper('Stmt_Use'));
    }
}
