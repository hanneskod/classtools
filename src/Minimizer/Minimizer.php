<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Minimizer;

use hanneskod\classtools\Iterator\ClassMapInterface;
use hanneskod\classtools\Translator\Reader;
use hanneskod\classtools\Translator\Action\CommentStripper;
use hanneskod\classtools\Translator\Action\NodeStripper;
use hanneskod\classtools\Translator\Action\NamespaceWrapper;
use PhpParser\NodeVisitor\NameResolver;

/**
 * Minimize mapped classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Minimizer
{
    private $classMap;

    /**
     * @param ClassMapInterface $classMap
     */
    public function __construct(ClassMapInterface $classMap)
    {
        $this->classMap = $classMap;
    }

    /**
     * Get a minimized version of the mapped code
     *
     * @return string
     */
    public function minimize()
    {
        // TODO working, more testing needed...
        $code = '';

        foreach ($this->classMap->getClassMap() as $classname => $path) {
            $code .= $this->getReaderFor($path)
                ->read($classname)
                ->apply(new CommentStripper)
                ->apply(new NameResolver)
                ->apply(new NodeStripper('PhpParser\Node\Stmt\Use_'))
                ->write() . "\n";
        }

        return "<?php $code";
    }

    private function getReaderFor($path)
    {
        // TODO cache
        return new Reader(file_get_contents($path));
    }
}
