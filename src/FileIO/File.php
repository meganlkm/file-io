<?php

namespace FileIO;

use FileIO\Exceptions\ReadOnlyException;

class File
{
    protected $file;
    protected $fileHandle;
    protected $mode;
    protected $type;

    protected $writeFunctions = [
        'csv' => 'fputcsv',
        'default' => 'fwrite'
    ];

    public function __construct($file, $mode = 'read', $type = 'txt')
    {
        $this->setFile($file);
        $this->setMode($mode);
        $this->type = $type;
    }

    public static function newInstance($file, $mode = 'read', $type = 'txt')
    {
        return new self($file, $mode, $type);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

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

    public function getMode()
    {
        return $this->mode;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function open()
    {
        $this->fileHandle = fopen($this->file, $this->mode);
        return $this;
    }

    public function close()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
            $this->fileHandle = null;
        }
    }

    public function getContents()
    {
        return file_get_contents($this->file);
    }

    public function getWriteFunction()
    {
        return (isset($this->writeFunctions[$this->type])) ?
            $this->writeFunctions[$this->type] :
            $this->writeFunctions['default'];
    }

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

    public function write($data)
    {
        if ($this->isWritable()) {
            $wfunc = $this->getWriteFunction();
            $wfunc($this->fileHandle, $data);
            return $this;
        }

        throw new ReadOnlyException;
    }

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

    // this definition could be different
    // for each class....
    public function isValid()
    {
        return ($this->exists() && $this->getFileSize() > 0);
    }

    public function getFileSize()
    {
        return filesize($this->file);
    }

    public function exists()
    {
        return file_exists($this->file);
    }

    public function isWritable()
    {
        return ($this->getMode() != 'r' && is_writable($this->file));
    }

    public function isOpen()
    {
        return (bool) ($this->fileHandle);
    }
}
