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
    $header = ['col1', 'col2', 'col3']
    $data = [
        ['row1col1', 'row1, col2', 'row1col3'],
        ['row2col1', 'row2, col2', 'row2col3'],
        ['row3col1', 'row3, col2', 'row3col3'],
    ];
    $file = File::newInstance('test.csv', 'write')->open();
    CSV::newInstance($file)
        ->setHeader($header)
        ->setData($data)
        ->save();
````
