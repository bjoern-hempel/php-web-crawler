# WebCrawler

This php class allows you to crawl recursively a given webpage (or a given html file) and collect some data from it. Simply define the url (or a html file) and a set of xpath expressions which should map with the output data object. The final representation will be a php array which can be easily converted into the json format for further processing.

## Installation

```bash
user$ git clone git@github.com:bjoern-hempel/php-web-crawler.git .
```

## Execute the examples

```bash
user$ php examples/simple.php 
{
    "version": "1.0.0",
    "title": "Test Title",
    "paragraph": "Test Paragraph"
}
```

## How to use

### Basic usage (simple html page)

[basic.html](examples/html/basic.html)

```html
<html>
    <head>
        <title>Test Page</title>
    </head>
    <body>
        <h1>Test Title</h1>
        <p>Test Paragraph</p>
    </body>
</html>
```

[simple.php](examples/simple.php)

```php5
<?php

include dirname(__FILE__).'/../autoload.php';

use Ixno\WebCrawler\Output\Field;
use Ixno\WebCrawler\Value\Text;
use Ixno\WebCrawler\Value\XpathTextnode;
use Ixno\WebCrawler\Source\File;

$file = dirname(__FILE__).'/html/basic.html';

$html = new File(
    $file,
    new Field('version', new Text('1.0.0')),
    new Field('title', new XpathTextnode('//h1')),
    new Field('paragraph', new XpathTextnode('//p'))
);

$data = json_encode($html->parse(), JSON_PRETTY_PRINT);

print_r($data);

echo "\n";
```

It returns:

```json
{
    "version": "1.0.0",
    "title": "Test Title",
    "paragraph": "Test Paragraph"
}
```

#### More examples

* [examples/simple-wiki-page.php](examples/simple-wiki-page.php)

### Complex examples

TODO...

## Running the tests

```bash
user$ phpunit tests/Basic.php 
PHPUnit 7.0.2 by Sebastian Bergmann and contributors.

..                                                                  2 / 2 (100%)

Time: 126 ms, Memory: 8.00MB

OK (2 tests, 16 assertions)
```

## Authors

* **Björn Hempel** - *Initial work* - [Björn Hempel](https://github.com/bjoern-hempel)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
