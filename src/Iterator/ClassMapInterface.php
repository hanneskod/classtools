<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Iterator;

/**
 * Defines a map of class definitions to filesystem paths
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface ClassMapInterface
{
    /**
     * Get iterator that yields classnames as keys and filesystem paths as values
     *
     * @return \Iterator
     */
    public function getClassMap();
}
