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
use hanneskod\classtools\Exception\ReaderException;

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
    private $decorated;

    /**
     * @var Reader Cached reader
     */
    private $reader;

    /**
     * Load decorated object
     *
     * @param FinderSplFileInfo $decorated
     */
    public function __construct(FinderSplFileInfo $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * Get reader for the contents of this file
     *
     * @return Reader
     * @throws ReaderException If file contains syntax errors
     */
    public function getReader()
    {
        if (!isset($this->reader)) {
            try {
                $this->reader = new Reader($this->getContents());
            } catch (ReaderException $exception) {
                throw new ReaderException($exception->getMessage() . ' in ' . $this->getPathname());
            }
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
        return $this->decorated->getRelativePath();
    }

    /**
     * Returns the relative path name
     *
     * @return string
     */
    public function getRelativePathname()
    {
        return $this->decorated->getRelativePathname();
    }

    /**
     * Returns the contents of the file
     *
     * @return string
     * @throws \RuntimeException
     */
    public function getContents()
    {
        return (string)$this->decorated->getContents();
    }

    /**
     * Gets the last access time for the file
     *
     * @return int
     */
    public function getATime()
    {
        return $this->decorated->getATime();
    }

    /**
     * Returns the base name of the file, directory, or link without path info
     *
     * @param  string $suffix
     * @return string
     */
    public function getBasename($suffix = '')
    {
        return $this->decorated->getBasename($suffix);
    }

    /**
     * Returns the inode change time for the file
     *
     * @return int
     */
    public function getCTime()
    {
        return $this->decorated->getCTime();
    }

    /**
     * Retrieves the file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->decorated->getExtension();
    }

    /**
     * Gets an SplFileInfo object for the referenced file
     *
     * @param  string $class_name
     * @return \SplFileInfo
     */
    public function getFileInfo($class_name = '')
    {
        return $this->decorated->getFileInfo($class_name);
    }

    /**
     * Gets the filename without any path information
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->decorated->getFilename();
    }

    /**
     * Gets the file group
     *
     * @return int
     */
    public function getGroup()
    {
        return $this->decorated->getGroup();
    }

    /**
     * Gets the inode number for the filesystem object
     *
     * @return int
     */
    public function getInode()
    {
        return $this->decorated->getInode();
    }

    /**
     * Gets the target of a filesystem link
     *
     * @return string
     */
    public function getLinkTarget()
    {
        return $this->decorated->getLinkTarget();
    }

    /**
     * Returns the time when the contents of the file were changed
     *
     * @return int
     */
    public function getMTime()
    {
        return $this->decorated->getMTime();
    }

    /**
     * Gets the file owner
     *
     * @return int
     */
    public function getOwner()
    {
        return $this->decorated->getOwner();
    }

    /**
     * Returns the path to the file, omitting the filename and any trailing slash
     *
     * @return string
     */
    public function getPath()
    {
        return $this->decorated->getPath();
    }

    /**
     * Gets an SplFileInfo object for the parent of the current file
     *
     * @param  string $class_name
     * @return \SplFileInfo
     */
    public function getPathInfo($class_name = '')
    {
        return $this->decorated->getPathInfo($class_name);
    }

    /**
     * Returns the path to the file
     *
     * @return string
     */
    public function getPathname()
    {
        return $this->decorated->getPathname();
    }

    /**
     * Gets the file permissions for the file
     *
     * @return int
     */
    public function getPerms()
    {
        return $this->decorated->getPerms();
    }

    /**
     * Expands all symbolic links and resolves relative references
     *
     * @return string
     */
    public function getRealPath()
    {
        return $this->decorated->getRealPath();
    }

    /**
     * Returns the filesize in bytes for the file referenced
     *
     * @return int
     */
    public function getSize()
    {
        return $this->decorated->getSize();
    }

    /**
     * Returns the type of the file referenced
     *
     * @return string
     */
    public function getType()
    {
        return $this->decorated->getType();
    }

    /**
     * This method can be used to determine if the file is a directory
     *
     * @return boolean
     */
    public function isDir()
    {
        return $this->decorated->isDir();
    }

    /**
     * Checks if the file is executable
     *
     * @return boolean
     */
    public function isExecutable()
    {
        return $this->decorated->isExecutable();
    }

    /**
     * Checks if the file referenced exists and is a regular file
     *
     * @return boolean
     */
    public function isFile()
    {
        return $this->decorated->isFile();
    }

    /**
     * Check if the file referenced is a link
     *
     * @return boolean
     */
    public function isLink()
    {
        return $this->decorated->isLink();
    }

    /**
     * Check if the file is readable
     *
     * @return boolean
     */
    public function isReadable()
    {
        return $this->decorated->isReadable();
    }

    /**
     * Check if the file is writable
     *
     * @return boolean
     */
    public function isWritable()
    {
        return $this->decorated->isWritable();
    }

    /**
     * Creates an SplFileObject object of the file
     *
     * @param  string   $open_mode
     * @param  boolean  $use_include_path
     * @param  resource $context
     * @return \SplFileObject
     */
    public function openFile($open_mode = "r", $use_include_path = false, $context = null)
    {
        return $this->decorated->openFile($open_mode, $use_include_path, $context);
    }

    /**
     * Set the class name which will be used to open files when openFile() is called
     *
     * @param  string $class_name
     * @return void
     */
    public function setFileClass($class_name = '')
    {
        $this->decorated->setFileClass($class_name);
    }

    /**
     * Set the class name which will be used when getFileInfo and getPathInfo are called
     *
     * @param  string $class_name
     * @return void
     */
    public function setInfoClass($class_name = '')
    {
        $this->decorated->setInfoClass($class_name);
    }

    /**
     * This method will return the file name of the referenced file
     *
     * @return string
     */
    public function __tostring()
    {
        return (string)$this->decorated;
    }
}
