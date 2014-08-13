<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Transformer;

use PhpParser\PrettyPrinter\Standard;

/**
 * Printer that always uses brackeded namespaces
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class BracketingPrinter extends Standard
{
    /**
     * Force canUseSemicolonNamespaces to false
     *
     * @param  array  $nodes
     * @return void
     */
    protected function preprocessNodes(array $nodes)
    {
        parent::preprocessNodes($nodes);
        $this->canUseSemicolonNamespaces = false;
    }
}
