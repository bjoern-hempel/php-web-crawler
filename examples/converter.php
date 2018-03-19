<?php
/*
 * MIT License
 *
 * Copyright (c) 2018 Björn Hempel <bjoern@hempel.li>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Ixno\WebCrawler;

include dirname(__FILE__).'/../autoload.php';

use Ixno\WebCrawler\Converter\DateParser;
use Ixno\WebCrawler\Converter\PregReplace;
use Ixno\WebCrawler\Converter\Trim;
use Ixno\WebCrawler\Output\Field;
use Ixno\WebCrawler\Query\XpathTextnode;
use Ixno\WebCrawler\Source\File;

$file = dirname(__FILE__).'/converter.html';

$html = new File(
    $file,
    new Field(
        'title',
        new XpathTextnode(
            '//*[@id="title-overview-widget"]/div[2]/div[2]/div/div[2]/div[2]/h1',
            new Trim()
        )
    ),
    new Field(
        'date',
        new XpathTextnode(
            '//*[@id="title-overview-widget"]/div[2]/div[2]/div/div[2]/div[2]/div[2]/a[4]',
            new Trim(),
            new PregReplace('~ \([^\(]+\)~', ''),
            new DateParser('d M Y H:i:s', '%s 12:00:00')
        )
    )
);

$data = json_encode($html->parse(), JSON_PRETTY_PRINT);

print_r($data);

echo "\n";
