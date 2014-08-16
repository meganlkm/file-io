<?php

namespace FileIO;

use FileIO\File;

class CSV
{
    protected $file;
    protected $header;
    protected $data;

    public function __construct(File $file)
    {
        $file->setType('csv');
        $this->file = $file;
    }

    public function __destruct()
    {
        $this->file = null;
    }

    public static function newInstance(File $file)
    {
        return new self($file);
    }

    public function setHeader($header = array())
    {
        $this->header = $header;
        return $this;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function setData($data = array())
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function writeHeader()
    {
        if (!empty($this->header)) {
            $this->file->write($this->header);
        }
        return $this;
    }

    public function writeData()
    {
        if (!empty($this->data)) {
            $this->file->writeArray($this->data);
        }
        return $this;
    }

    /**
     * wrapper for writeHeader and writeData
     * @return object this
     */
    public function save()
    {
        $this->writeHeader();
        $this->writeData();
        return $this;
    }
}
