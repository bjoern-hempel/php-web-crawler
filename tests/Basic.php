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
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class BasicTest extends TestCase
{
    protected $version = '1.0.0';

    /**
     * Helper function: Parse given html file and return the data array.
     *
     * @param $path
     * @param array $fields
     * @return mixed
     * @throws \ReflectionException
     */
    protected function parseHtml($path, $fields = array())
    {
        $file = dirname(__FILE__).$path;

        $reflector = new ReflectionClass('Ixno\WebCrawler\Source\File');

        $parameter = array_merge(
            array(
                $file,
                new Field('version', new Text($this->version)),
            ),
            $fields
        );

        $html = $reflector->newInstanceArgs($parameter);

        return $html->parse();
    }

    /**
     * Test: Parse basic html file.
     */
    public function testSimple()
    {
        /* Arrange */
        $path = '/../examples/html/basic.html';
        $fields = array(
            new Field('title', new XpathTextnode('//h1')),
            new Field('paragraph', new XpathTextnode('//p'))
        );

        /* Act */
        $data = $this->parseHtml($path, $fields);

        /* Assert */
        $this->assertIsArray($data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('paragraph', $data);
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['version'], $this->version);
        $this->assertEquals($data['title'], 'Test Title');
        $this->assertEquals($data['paragraph'], 'Test Paragraph');
    }

    /**
     * Test: Parse wiki page.
     */
    public function testSimpleWikiPage()
    {
        /* Arrange */
        $path = '/../examples/html/wiki-page.html';
        $fields = array(
            new Field('title', new XpathTextnode('//*[@id="firstHeading"]/i')),
            new Field('directed_by', new XpathTextnode('//*[@id="mw-content-text"]/div/table[1]//tr[3]/td/a'))
        );

        /* Act */
        $data = $this->parseHtml($path, $fields);

        /* Assert */
        $this->assertIsArray($data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('directed_by', $data);
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['version'], $this->version);
        $this->assertEquals($data['title'], 'Pirates of the Caribbean: The Curse of the Black Pearl');
        $this->assertEquals($data['directed_by'], 'Gore Verbinski');
    }

    /**
     * Test: Parse another html page.
     */
    public function testAnotherPage()
    {
        /* Arrange */
        $path = '/../examples/html/praesidium.html';
        $fields = array(
            new Field('title', new XpathTextnode('//html/head/title')),
            new Field('cookies', new XpathTextnode('//html/body/div[1]/div/div[1]'))
        );

        /* Act */
        $data = $this->parseHtml($path, $fields);

        /* Assert */
        $this->assertIsArray($data);
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('cookies', $data);
        $this->assertEquals(count($data), 3);
        $this->assertEquals($data['version'], $this->version);
        $this->assertEquals($data['title'], 'Präsidium | ADAC e.V.');
        $this->assertEquals(trim($data['cookies']), 'Wir verwenden Cookies. Mit der weiteren Nutzung unserer Seite stimmen Sie dem zu. Details und Widerspruchsmöglichkeiten finden Sie in unseren Datenschutzhinweisen.');
    }
}