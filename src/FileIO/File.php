<?php

namespace FileIO;

use FileIO\Exceptions\ReadOnlyException;

/**
 * FileIO\File
 *
 * wrapper for file io functionality
 *
 * @author  Megan Wood <megan.lkm@gmail.com>
 * @package FileIO
 * @version 1.0
 */
class File
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var resource
     */
    protected $fileHandle;

    /**
     * type of access to open file with
     * @var string
     */
    protected $mode;

    /**
     * file type
     * @var string
     */
    protected $type;

    /**
     * php functions for writing files
     * @var array
     */
    protected $writeFunctions = [
        'csv' => 'fputcsv',
        'default' => 'fwrite'
    ];

    /**
     * @param string $file name and path to file
     * @param string $mode how to access the file
     * @param string $type the file format/extension
     */
    public function __construct($file, $mode = 'read', $type = 'txt')
    {
        $this->setFile($file);
        $this->setMode($mode);
        $this->type = $type;
    }

    /**
     * construct a new instance and return
     * new instance of File to support
     * method chaining
     *
     * @param string $file name and path to file
     * @param string $mode how to access the file
     * @param string $type the file format/extension
     * @return FileIO\File       return new instance of File
     */
    public static function newInstance($file, $mode = 'read', $type = 'txt')
    {
        return new self($file, $mode, $type);
    }

    /**
     * close the file
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * set the file path/name
     *
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * get the file path/name
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * set the access mode
     *
     * @param string $mode
     */
    public function setMode($mode)
    {
        switch ($mode) {
            case 'append':
                $this->mode = 'a';
                break;
            case 'write':
                $this->mode = 'w';
                break;
            default:
                $this->mode = 'r';
                break;
        }
    }

    /**
     * get the access mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * set the file type
     *
     * @param string
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * get the file type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * open the file name passed to constructor
     *
     * @return FileIO\File return this instance to support method chaining
     */
    public function open()
    {
        $this->fileHandle = fopen($this->file, $this->mode);
        return $this;
    }

    /**
     * close file
     */
    public function close()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
            $this->fileHandle = null;
        }
    }

    /**
     * get the contents of a file
     *
     * @return string
     */
    public function getContents()
    {
        return file_get_contents($this->file);
    }

    /**
     * get the write function used for file type
     *
     * @return string
     */
    public function getWriteFunction()
    {
        return (isset($this->writeFunctions[$this->type])) ?
            $this->writeFunctions[$this->type] :
            $this->writeFunctions['default'];
    }

    /**
     * Write array to file. Each array element
     * represents a line in the file.
     *
     * @param  array  $data
     * @return FileIO\File return this instance to support method chaining
     * @throws ReadOnlyException if the file is not writable
     */
    public function writeArray($data = array())
    {
        if (!$this->isWritable()) {
            throw new ReadOnlyException;
        }

        foreach ($data as $dataRow) {
            $this->write($dataRow);
        }
        return $this;
    }

    /**
     * write data to file
     *
     * @param  mixed $data write a line to a file
     * @return FileIO\File return this instance to support method chaining
     * @throws ReadOnlyException if the file is not writable
     */
    public function write($data)
    {
        if ($this->isWritable()) {
            $wfunc = $this->getWriteFunction();
            $wfunc($this->fileHandle, $data);
            return $this;
        }

        throw new ReadOnlyException;
    }

    /**
     * switch file access mode to append
     * write data at the end of the file
     *
     * @param  mixed $data line to write to file
     * @return FileIO\File return this instance to support method chaining
     */
    public function append($data)
    {
        if ($this->getMode() != 'a') {
            $this->close();
            $this->setMode('append');
            $this->open();
        }
        $this->write($data);
        return $this;
    }

    /**
     * considered valid if the file exists
     * and the file size is greater than 0
     *
     * @todo  this definition could be different for each class....
     * @todo  create Validator Bundle
     * @return boolean
     */
    public function isValid()
    {
        return ($this->exists() && $this->getFileSize() > 0);
    }

    /**
     * get the size of this file
     *
     * @return int
     */
    public function getFileSize()
    {
        return filesize($this->file);
    }

    /**
     * @return boolean
     */
    public function exists()
    {
        return file_exists($this->file);
    }

    /**
     * @return boolean
     */
    public function isWritable()
    {
        return ($this->getMode() != 'r' && is_writable($this->file));
    }

    /**
     * @return boolean
     */
    public function isOpen()
    {
        return (bool) ($this->fileHandle);
    }
}
