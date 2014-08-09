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
use hanneskod\classtools\Extractor\Extractor;
use hanneskod\classtools\Extractor\Visitor\CommentStripper;
use hanneskod\classtools\Extractor\Visitor\NodeStripper;
use hanneskod\classtools\Extractor\Visitor\NamespaceWrapper;
use PhpParser\NodeVisitor\NameResolver;

/**
 * Minimize mapped classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Minimizer
{
    private $classMap, $extractors = array();

    /**
     * @param ClassMapInterface $classMap
     */
    public function __construct(ClassMapInterface $classMap)
    {
        $this->classMap = $classMap;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->minimize();
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
            $code .= $this->getExtractorFor($path)
                ->extract($classname)
                ->registerVisitor(new CommentStripper)
                ->registerVisitor(new NameResolver)
                ->registerVisitor(new NodeStripper('PhpParser\Node\Stmt\Use_'))
                ->registerVisitor(new NamespaceWrapper)
                ->getCode() . "\n";
        }

        return "<?php $code";
    }

    private function getExtractorFor($path)
    {
        // TODO cache
        return new Extractor(file_get_contents($path));
    }
}
