FileIO
====

A basic file input/output package.

<hr />
**Usage:**

Write a string to file:
````
    $file = File::newInstance('/path/to/file', 'write')
        ->open()
        ->write('hello');
````

Append an array to file:
````
    $file = File::newInstance('/path/to/file', 'append')
        ->open()
        ->writeArray(['hello', 'foo']);
````

Write a csv file:
````
    $file = File::newInstance('test.csv', 'write')->open();
    CSV::newInstance($file)
        ->setHeader($this->header)
        ->setData($this->data)
        ->save();
````
