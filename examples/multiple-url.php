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
use Ixno\WebCrawler\Converter\Sprintf;
use Ixno\WebCrawler\Converter\Trim;
use Ixno\WebCrawler\Output\Field;
use Ixno\WebCrawler\Output\Group;
use Ixno\WebCrawler\Source\Url;
use Ixno\WebCrawler\Source\XpathSection;
use Ixno\WebCrawler\Value\XpathTextnode;
use Ixno\WebCrawler\Source\File;
use Ixno\WebCrawler\Source\XpathSections;

$source = dirname(__FILE__).'/adac.html';
$sourceSub = dirname(__FILE__).'/adac-sub.html';

$html = new File(
    $source,
    new Field('title', new XpathTextnode('/html/body/section[1]/div/div[2]/div/div/div[1]/h1')),
    new Group(
        'hits',
        new XpathSections(
            '/html/body/section[1]/div/div[2]/div/div/article',
            new Field(
                'url',
                new XpathTextnode(
                    './p[1]/a[1][span[contains(text(), \'Mehr\')]]/@href',
                    new Sprintf('https://presse.adac.de%s')
                )
            ),
            new Field(
                new XpathTextnode(
                    './p[1]/a[1][span[contains(text(), \'Mehr\')]]/@href',
                    new Sprintf('https://presse.adac.de%s'),
                    new File(
                        $sourceSub,
                        new XpathSection(
                            '/html/body/section[1]/div/div[2]/div/div/article',
                            new Field('title', new XpathTextnode('./h1[1]', new Trim())),
                            new Field('subtitle', new XpathTextnode('./p[contains(concat(" ", normalize-space(@class), " "), " box-intro ")][1]', new Trim())),
                            new Field('category', new XpathTextnode('./span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[1]', new Trim())),
                            new Field('subcategory', new XpathTextnode('./span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[2]', new Trim())),
                            new Field(
                                'date',
                                new XpathTextnode(
                                    './span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[3]',
                                    new Trim(),
                                    new DateParser('d.m.Y H:i:s', '%s 12:00:00')
                                )
                            ),
                            new Field('text', new XpathTextnode('./div[contains(concat(" ", normalize-space(@class), " "), " box-content-grid ")][1]/p'))
                        )
                    )
                )
            )
        )
    )
);

$data = json_encode($html->parse(), JSON_PRETTY_PRINT);

print_r($data);

echo "\n";
