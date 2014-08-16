<?php

namespace FileIO\Tests;

use FileIO\CSV;
use FileIO\File;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit_Framework_TestCase;

class CSVTest extends PHPUnit_Framework_TestCase
{
    protected $filesystem;
    protected $file;
    protected $header = ['col1', 'col2', 'col3'];
    protected $data = [
        ['row1col1', 'row1, col2', 'row1col3'],
        ['row2col1', 'row2, col2', 'row2col3'],
        ['row3col1', 'row3, col2', 'row3col3'],
    ];

    public function setUp()
    {
        $this->filesystem = vfsStream::setup('fs');
        $this->file = File::newInstance(vfsStream::url('fs/test.csv'), 'write')->open();
    }

    public function testSetAndGetHeader()
    {
        $csv = CSV::newInstance($this->file)
            ->setHeader($this->header);
        $this->assertSame($this->header, $csv->getHeader());
    }

    public function testSetAndGetData()
    {
        $csv = CSV::newInstance($this->file)
            ->setData($this->data);
        $this->assertSame($this->data, $csv->getData());
    }

    public function testWriteHeader()
    {
        CSV::newInstance($this->file)
            ->setHeader($this->header)
            ->writeHeader();
        $this->assertSame($this->getCsvStr([$this->header]), $this->file->getContents());
    }

    public function testWriteData()
    {
        CSV::newInstance($this->file)
            ->setData($this->data)
            ->writeData();
        $this->assertSame($this->getCsvStr($this->data), $this->file->getContents());
    }

    public function testSaveHeaderAndData()
    {
        CSV::newInstance($this->file)
            ->setHeader($this->header)
            ->setData($this->data)
            ->save();
        $expected = $this->getCsvStr([$this->header]);
        $expected .= $this->getCsvStr($this->data);
        $this->assertSame($expected, $this->file->getContents());
    }

    protected function getCsvStr($csvArray)
    {
        ob_start();
        $out = fopen('php://output', 'w');
        foreach ($csvArray as $csvRow) {
            fputcsv($out, $csvRow);
        }
        fclose($out);
        return ob_get_clean();
    }
}
