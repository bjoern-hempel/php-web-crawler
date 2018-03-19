<?php

namespace Ixno\WebCrawler;

include '../autoload.php';

use Ixno\WebCrawler\Output\Field;
use Ixno\WebCrawler\Output\Group;
use Ixno\WebCrawler\Query\XpathField;
use Ixno\WebCrawler\Source\XpathSections;
use Ixno\WebCrawler\Source\File;

$sourceFile = 'search.html';

$html = new File(
    $sourceFile,
    new Field('title', new XpathField('//*[@id="firstHeading"]')),
    new Group(
        'hits',
        new XpathSections(
            '//*[@id="mw-content-text"]/div/ul/li',
            new Field('title', new XpathField('./div[1]/a')),
            new Field('link', new XpathField('./div[1]/a/@href'))
        )
    )
);

$data = json_encode($html->parse(), JSON_PRETTY_PRINT);

print_r($data);

echo "\n";