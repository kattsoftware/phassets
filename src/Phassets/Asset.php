<?php

namespace Phassets;

class Asset
{
    private $fullPath;

    private $isFile;

    private $pathinfo;

    private $fileModifiedTimestamp;

    private $md5Checksum;

    private $contents;

    public function __construct($fullPath)
    {
        $this->fullPath = $fullPath;
        $this->isFile = is_file($fullPath);
        $this->pathinfo = $this->isFile ? pathinfo($fullPath) : array();
    }

    public function getModifiedTimestamp()
    {
        if ($this->isFile) {
            return $this->fileModifiedTimestamp ?: $this->fileModifiedTimestamp = filemtime($this->fullPath);
        }

        return false;
    }

    public function getMd5()
    {
        if ($this->isFile) {
            return $this->md5Checksum ?: $this->md5Checksum = md5_file($this->fullPath);
        }

        return false;
    }

    public function getExtension()
    {
        return isset($this->pathinfo['extension']) ? $this->pathinfo['extension'] : false;
    }

    public function getContents()
    {
        if ($this->isFile) {
            return $this->contents ?: $this->contents = file_get_contents($this->fullPath);
        }

        return '';
    }

    public function setContents($contents)
    {
        $this->contents = $contents;
    }
}