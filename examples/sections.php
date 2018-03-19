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

use Ixno\WebCrawler\Output\Field;
use Ixno\WebCrawler\Output\Group;
use Ixno\WebCrawler\Value\XpathTextnode;
use Ixno\WebCrawler\Source\Url;
use Ixno\WebCrawler\Source\XpathSections;

$url = 'https://en.wikipedia.org/w/index.php?title=Special:Search&profile=advanced&search=Pirates+of+the+Caribbean+movie&searchToken=1r1zctaiucfw60o9mwdgr5nku';

$html = new Url(
    $url,
    new Field('title', new XpathTextnode('//*[@id="firstHeading"]')),
    new Group(
        'hits',
        new XpathSections(
            '//*[@id="mw-content-text"]/div/ul/li',
            new Field('title', new XpathTextnode('./div[1]/a')),
            new Field('link', new XpathTextnode('./div[1]/a/@href'))
        )
    )
);

$data = json_encode($html->parse(), JSON_PRETTY_PRINT);

print_r($data);

echo "\n";