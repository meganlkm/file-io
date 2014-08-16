<?php

namespace FileIO\Tests;

use FileIO\CSV;
use FileIO\File;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit_Framework_TestCase;

class FileTest extends PHPUnit_Framework_TestCase
{
    protected $filesystem;
    protected $file;
    protected $starttext = "this is the contents of the file";

    public function setUp()
    {
        $this->filesystem = vfsStream::setup('fs');
        $this->file = vfsStream::url('fs/test.txt');
        file_put_contents($this->file, $this->starttext);
    }

    public function testOpenFile()
    {
        $file = File::newInstance($this->file)->open();
        $this->assertTrue($file->isOpen());

        // open file that does not exist
        $newfile = new File(vfsStream::url('fs/newfile.txt'), 'write');
        $newfile->open();
        $this->assertTrue($newfile->isOpen());
    }

    /**
     * @expectedException FileIO\Exceptions\ReadOnlyException
     */
    public function testAttemptWriteToReadOnlyFile()
    {
        $file = File::newInstance($this->file)
            ->open()
            ->write('hello');
    }

    /**
     * @expectedException FileIO\Exceptions\ReadOnlyException
     */
    public function testAttemptWriteArrayToReadOnlyFile()
    {
        $file = File::newInstance($this->file)
            ->open()
            ->writeArray(['hello', 'foo']);
    }

    public function testFileExistsAndNotEmpty()
    {
        $file = File::newInstance($this->file)->open();
        $this->assertTrue($file->isValid());
    }

    public function testGetFileContents()
    {
        $file = new File($this->file);
        $fileContent = $file->getContents();
        $this->assertSame($this->starttext, $fileContent);
    }

    public function testCSVWrite()
    {
        $file = new File(vfsStream::url('fs/test.csv'), 'write', 'csv');
        $file->open();
        $this->assertTrue($file->isWritable());

        $header = ['one','two','three'];
        $file->write($header);
        $fileContent = $file->getContents();

        $header = implode(',', $header) . PHP_EOL;
        $this->assertSame($header, $fileContent);
    }

    public function testCSVWriteArray()
    {
        $file = new File(vfsStream::url('fs/test2.csv'), 'write', 'csv');
        $file->open();
        $this->assertTrue($file->isWritable());

        $data = [['one','two','three'],['four','five','six']];
        $file->writeArray($data);
        $fileContent = $file->getContents();

        $expect = '';
        foreach ($data as $row) {
            $expect .= implode(',', $row) . PHP_EOL;
        }

        $this->assertSame($expect, $fileContent);
    }

    public function testTextWrite()
    {
        $data = 'one two three';
        $file = File::newInstance($this->file, 'write')
            ->open()
            ->write($data);

        $fileContent = $file->getContents();
        $this->assertSame($data, $fileContent);
    }

    public function testAppendToFile()
    {
        $newdata = "\nHello World\n";

        $file = File::newInstance($this->file, 'append')
            ->open()
            ->append($newdata);

        $expected = $this->starttext . $newdata;
        $this->assertSame($expected, $file->getContents());
    }
}
