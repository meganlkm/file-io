<?php

namespace FileIO;

use FileIO\File;

/**
 * FileIO\CSV
 *
 * wrapper for CSV file functionality
 *
 * @author  Megan Wood <megan@devstuff.io>
 * @package FileIO
 * @version 1.0
 */
class CSV
{
    /**
     * @var FileIO\File
     */
    protected $file;

    /**
     * @var array
     */
    protected $header;

    /**
     * @var array
     */
    protected $data;

    /**
     * do something with a csv file
     *
     * @param File $file File object to write/read csv data
     */
    public function __construct(File $file)
    {
        $file->setType('csv');
        $this->file = $file;
    }

    /**
     * destroy File reference
     */
    public function __destruct()
    {
        $this->file = null;
    }

    /**
     * construct a new instance and chain
     *     other methods
     *
     * @param  File   $file File object to write/read csv data
     * @return FileIO\CSV       return new instance of CSV
     */
    public static function newInstance(File $file)
    {
        return new self($file);
    }

    /**
     * set the csv header row
     *
     * @param array $header
     * @return  FileIO\CSV return this instance to support method chaining
     */
    public function setHeader($header = array())
    {
        $this->header = $header;
        return $this;
    }

    /**
     * get the header row
     *
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * set the csv data
     *
     * @param array $data array of arrays - each csv row is an array
     * @return  FileIO\CSV return this instance to support method chaining
     */
    public function setData($data = array())
    {
        $this->data = $data;
        return $this;
    }

    /**
     * get csv data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * write the header array to this File
     *
     * @return  FileIO\CSV return this instance to support method chaining
     */
    public function writeHeader()
    {
        if (!empty($this->header)) {
            $this->file->write($this->header);
        }
        return $this;
    }

    /**
     * write the data array to this File
     *
     * @return  FileIO\CSV return this instance to support method chaining
     */
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
