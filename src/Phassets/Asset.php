<?php

namespace Phassets;

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
     * @var array pathinfo() call result on "fullPath"
     */
    private $pathinfo;

    /**
     * @var string Initial or modified contents of the file
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
    private $md5Checksum;

    /**
     * @var string The SHA1 checksum of asset
     */
    private $sha1;

    /**
     * Asset constructor.
     *
     * @param string $fullPath Full, absolute path to asset/identifier of asset
     */
    public function __construct($fullPath)
    {
        $this->fullPath = $fullPath;
        $this->isFile = is_file($fullPath);
        $this->pathinfo = $this->isFile ? pathinfo($fullPath) : [];
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    /**
     * @return bool|mixed
     */
    public function getFilename()
    {
        return isset($this->pathinfo['filename']) ? $this->pathinfo['filename'] : false;
    }

    /**
     * @return bool|mixed
     */
    public function getExtension()
    {
        return isset($this->pathinfo['extension']) ? $this->pathinfo['extension'] : false;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        if ($this->isFile) {
            return $this->contents ?: $this->contents = file_get_contents($this->fullPath);
        }

        return '';
    }

    /**
     * @param string $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        if ($this->isFile) {
            return $this->size ?: $this->size = filesize($this->fullPath);
        }

        return false;
    }

    /**
     * @return bool|int
     */
    public function getModifiedTimestamp()
    {
        if ($this->isFile) {
            return $this->fileModifiedTimestamp ?: $this->fileModifiedTimestamp = filemtime($this->fullPath);
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getMd5()
    {
        if ($this->isFile) {
            return $this->md5Checksum ?: $this->md5Checksum = md5_file($this->fullPath);
        }

        return false;
    }

    /**
     * @return bool|string
     */
    public function getSha1()
    {
        if ($this->isFile) {
            return $this->sha1 ?: $this->sha1 = sha1_file($this->fullPath);
        }

        return false;
    }
}