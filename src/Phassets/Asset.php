<?php

namespace Phassets;

use Phassets\Interfaces\FileHandler;

/**
 * Asset definition class
 *
 * This content is released under the MIT License (MIT).
 *
 * @see LICENSE file
 */
class Asset
{
    /**
     * @var string Full path sent upon construction of object
     */
    private $fullPath;

    /**
     * @var bool is "fullPath" a real path, to a real file?
     */
    private $isFile;

    /**
     * @var string The asset dirname (from pathinfo())
     */
    private $dirname;

    /**
     * @var string The asset filename (from pathinfo())
     */
    private $filename;

    /**
     * @var string The asset extension (from pathinfo())
     */
    private $extension;

    /**
     * @var string The contents of a plain/text asset
     */
    private $contents;

    /**
     * @var int Result returned from filesize()
     */
    private $size;

    /**
     * @var int UNIX timestamp of asset's last modified date
     */
    private $fileModifiedTimestamp;

    /**
     * @var string MD5 hash from md5_file()
     */
    private $md5;

    /**
     * @var string The SHA1 checksum of asset
     */
    private $sha1;

    /**
     * @var string The changed extension of the asset (initial value is $this->extension)
     */
    private $outputExtension;

    /**
     * @var string URL to deployed version of this asset
     */
    private $outputUrl;

    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * Asset constructor.
     *
     * @param string $fullPath Full, absolute path to asset/identifier of asset
     */
    public function __construct($fullPath)
    {
        $this->fullPath = $fullPath;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->outputUrl;
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    /**
     * @return boolean Whether original file is a real file or not
     */
    public function isFile()
    {
        return is_bool($this->isFile) ? $this->isFile : $this->isFile = is_file($this->fullPath);
    }

    /**
     * @return string
     */
    public function getDirname()
    {
        return is_string($this->dirname) ? $this->dirname : $this->dirname = pathinfo($this->fullPath, PATHINFO_DIRNAME);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return is_string($this->filename) ? $this->filename : $this->filename = pathinfo($this->fullPath, PATHINFO_FILENAME);
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return is_string($this->extension) ? $this->extension : $this->extension = pathinfo($this->fullPath, PATHINFO_EXTENSION);
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        if ($this->fileHandler instanceof FileHandler) {
            return $this->fileHandler->getContents();
        }

        if ($this->isFile()) {
            return is_string($this->contents) ? $this->contents : $this->contents = file_get_contents($this->fullPath);
        }

        return '';
    }

    /**
     * @param mixed $contents
     */
    public function setContents($contents)
    {
        if ($this->fileHandler instanceof FileHandler) {
            $this->fileHandler->setContents($contents);

            return;
        }

        $this->contents = $contents;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        if ($this->isFile()) {
            return is_int($this->size) ? $this->size : $this->size = filesize($this->fullPath);
        }

        return 0;
    }

    /**
     * @return bool|int
     */
    public function getModifiedTimestamp()
    {
        if ($this->isFile()) {
            return $this->fileModifiedTimestamp ?: $this->fileModifiedTimestamp = filemtime($this->fullPath);
        }

        return false;
    }

    /**
     * @return bool|string The MD5 string computed for the original file
     */
    public function getMd5()
    {
        if ($this->isFile()) {
            return $this->md5 ?: $this->md5 = md5_file($this->fullPath);
        }

        return false;
    }

    /**
     * @return bool|string The SHA1 string computed for the original file
     */
    public function getSha1()
    {
        if ($this->isFile()) {
            return $this->sha1 ?: $this->sha1 = sha1_file($this->fullPath);
        }

        return false;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getOutputExtension()
    {
        return $this->outputExtension ?: $this->outputExtension = $this->getExtension();
    }

    /**
     * @param string $outputExtension
     */
    public function setOutputExtension($outputExtension)
    {
        $this->outputExtension = $outputExtension;
    }

    /**
     * @return string
     */
    public function getOutputUrl()
    {
        return $this->outputUrl;
    }

    /**
     * @param string $outputUrl
     */
    public function setOutputUrl($outputUrl)
    {
        $this->outputUrl = $outputUrl;
    }

    /**
     * @param FileHandler $fileHandler
     */
    public function registerFileHandler(FileHandler $fileHandler)
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * @return FileHandler
     */
    public function getFileHandler()
    {
        return $this->fileHandler;
    }
}
