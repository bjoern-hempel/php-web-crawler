<?php

namespace Ixno\WebCrawler;

include '../autoload.php';

use Ixno\WebCrawler\Output\Field;
use Ixno\WebCrawler\Output\Group;
use Ixno\WebCrawler\Value\XpathOuterhtml;
use Ixno\WebCrawler\Source\XpathSections;
use Ixno\WebCrawler\Source\File;

$sourceFile = 'search.html';

$html = new File(
    $sourceFile,
    new Field('outerhtml', new XpathOuterhtml('//*[@id="p-namespaces"]'))
);

$data = json_encode($html->parse(), JSON_PRETTY_PRINT);

print_r($data);

echo "\n";