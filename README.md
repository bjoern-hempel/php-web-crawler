# WebCrawler

This php class allows you to crawl recursively a given webpage (or a given html file) and collect some data from it. Simply define the url (or a html file) and a set of xpath expressions which should map with the output data object. The final representation will be a php array which can be easily converted into the json format for further processing.

## How to use

### Basic usage (simple html page)

basic.html

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
