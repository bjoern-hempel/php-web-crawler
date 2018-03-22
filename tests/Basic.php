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
use Ixno\WebCrawler\Value\Text;
use Ixno\WebCrawler\Value\XpathTextnode;
use Ixno\WebCrawler\Source\File;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function test()
    {
        $file = dirname(__FILE__).'/../examples/simple.html';

        $version = '1.0.0';

        $html = new File(
            $file,
            new Field('version', new Text($version)),
            new Field('title', new XpathTextnode('//*[@id="firstHeading"]/i')),
            new Field('directed_by', new XpathTextnode('//*[@id="mw-content-text"]/div/table[1]//tr[3]/td/a'))
        );

        $data = $html->parse();

        $this->assertInternalType('array', $data);

        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('directed_by', $data);

        $this->assertEquals(count($data), 3);

        $this->assertEquals($data['version'], $version);
        $this->assertEquals($data['title'], 'Pirates of the Caribbean: The Curse of the Black Pearl');
        $this->assertEquals($data['directed_by'], 'Gore Verbinski');
    }
}