<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace hanneskod\classtools\Iterator;

use Symfony\Component\Finder\SplFileInfo as FinderSplFileInfo;
use hanneskod\classtools\Transformer\Reader;

/**
 * Decorates \Symfony\Component\Finder\SplFileInfo to support the creation of Readers
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class SplFileInfo extends FinderSplFileInfo
{
    /**
     * @var FinderSplFileInfo Decorated object
     */
    private $decoratedFileInfo;

    /**
     * @var Reader Cached reader
     */
    private $reader;

    /**
     * Load decorated object
     *
     * @param FinderSplFileInfo $decoratedFileInfo
     */
    public function __construct(FinderSplFileInfo $decoratedFileInfo)
    {
        $this->decoratedFileInfo = $decoratedFileInfo;
    }

    /**
     * Get reader for the contents of this file
     *
     * @return Reader
     */
    public function getReader()
    {
        if (!isset($this->reader)) {
            $this->reader = new Reader($this->getContents());
        }

        return $this->reader;
    }

    /**
     * Returns the relative path
     *
     * @return string
     */
    public function getRelativePath()
    {
        return $this->decoratedFileInfo->getRelativePath();
    }

    /**
     * Returns the relative path name
     *
     * @return string
     */
    public function getRelativePathname()
    {
        return $this->decoratedFileInfo->getRelativePathname();
    }

    /**
     * Returns the contents of the file
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getContents()
    {
        return $this->decoratedFileInfo->getContents();
    }

    public function getATime()
    {
        return $this->decoratedFileInfo->getATime();
    }

    public function getBasename($suffix = '')
    {
        return $this->decoratedFileInfo->getBasename($suffix);
    }

    public function getCTime()
    {
        return $this->decoratedFileInfo->getCTime();
    }

    public function getExtension()
    {
        return $this->decoratedFileInfo->getExtension();
    }

    public function getFileInfo($class_name = '')
    {
        return $this->decoratedFileInfo->getFileInfo($class_name);
    }

    public function getFilename()
    {
        return $this->decoratedFileInfo->getFilename();
    }

    public function getGroup()
    {
        return $this->decoratedFileInfo->getGroup();
    }

    public function getInode()
    {
        return $this->decoratedFileInfo->getInode();
    }

    public function getLinkTarget()
    {
        return $this->decoratedFileInfo->getLinkTarget();
    }

    public function getMTime()
    {
        return $this->decoratedFileInfo->getMTime();
    }

    public function getOwner()
    {
        return $this->decoratedFileInfo->getOwner();
    }

    public function getPath()
    {
        return $this->decoratedFileInfo->getPath();
    }

    public function getPathInfo($class_name = '')
    {
        return $this->decoratedFileInfo->getPathInfo($class_name);
    }

    public function getPathname()
    {
        return $this->decoratedFileInfo->getPathname();
    }

    public function getPerms()
    {
        return $this->decoratedFileInfo->getPerms();
    }

    public function getRealPath()
    {
        return $this->decoratedFileInfo->getRealPath();
    }

    public function getSize()
    {
        return $this->decoratedFileInfo->getSize();
    }

    public function getType()
    {
        return $this->decoratedFileInfo->getType();
    }

    public function isDir()
    {
        return $this->decoratedFileInfo->isDir();
    }

    public function isExecutable()
    {
        return $this->decoratedFileInfo->isExecutable();
    }

    public function isFile()
    {
        return $this->decoratedFileInfo->isFile();
    }

    public function isLink()
    {
        return $this->decoratedFileInfo->isLink();
    }

    public function isReadable()
    {
        return $this->decoratedFileInfo->isReadable();
    }

    public function isWritable()
    {
        return $this->decoratedFileInfo->isWritable();
    }

    public function openFile($open_mode = "r", $use_include_path = false, $context = null)
    {
        return $this->decoratedFileInfo->openFile($open_mode, $use_include_path, $context);
    }

    public function setFileClass($class_name = '')
    {
        return $this->decoratedFileInfo->setFileClass($class_name);
    }

    public function setInfoClass($class_name = '')
    {
        return $this->decoratedFileInfo->setInfoClass($class_name);
    }

    public function __toString()
    {
        return (string)$this->decoratedFileInfo;
    }
}
