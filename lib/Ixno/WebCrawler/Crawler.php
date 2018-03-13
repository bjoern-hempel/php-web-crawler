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

require_once 'Source/Source.php';
require_once 'Source/Data.php';
require_once 'Source/Html.php';
require_once 'Source/Url.php';
require_once 'Converter/Converter.php';
require_once 'Converter/DateParser.php';

use ArrayIterator;
use Exception;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXpath;

use Ixno\WebCrawler\Source\Source;
use Ixno\WebCrawler\Source\Html;
use Ixno\WebCrawler\Source\Url;
use Ixno\WebCrawler\Source\Data;

use Ixno\WebCrawler\Converter\Converter;

interface CrawlRule {
    public function getMapping();
    public function getDomElements(DOMXpath $xpath);
    public function getCrawler();
}

class Page extends ArrayIterator implements CrawlRule
{
    protected $crawler = null;

    public function __construct(XpathSet $mapping, Crawler $crawler = null)
    {
        parent::__construct($mapping);

        $this->crawler = $crawler;
    }

    public function getMapping()
    {
        return $this;
    }

    public function getDomElements(DOMXpath $xpath)
    {
        return $xpath->query('/')->item(0);
    }

    public function getCrawler()
    {
        return $this->crawler;
    }
}

class PageGroup implements CrawlRule
{
    protected $xpath = null;

    protected $page = null;

    protected $crawler = null;

    public function __construct(Xpath $xpath, Page $page, Crawler $crawler = null)
    {
        $this->xpath = $xpath;
        $this->page = $page;
        $this->crawler = $crawler;
    }

    public function getMapping()
    {
        return $this->page;
    }

    public function getDomElements(DOMXpath $xpath)
    {
        $domNodeList = $xpath->query($this->xpath);

        if (count($domNodeList) <= 0) {
            return null;
        }

        return $domNodeList->item(0);
    }

    public function getCrawler()
    {
        return $this->crawler;
    }
}

class PageList implements CrawlRule
{
    protected $xpath = null;

    protected $page = null;

    protected $crawler = null;

    public function __construct(Xpath $xpath, Page $page, Crawler $crawler = null)
    {
        $this->xpath = $xpath;
        $this->page = $page;
        $this->crawler = $crawler;
    }

    public function getMapping()
    {
        return $this->page;
    }

    public function getDomElements(DOMXpath $xpath)
    {
        $domNodeList = $xpath->query($this->xpath);

        if (count($domNodeList) <= 0) {
            return null;
        }

        return $domNodeList;
    }

    public function getCrawler()
    {
        return $this->crawler;
    }
}



class Xpath
{
    protected $xpath;

    public function __construct($xpath)
    {
        $this->xpath = $xpath;
    }

    public function __toString()
    {
        return $this->xpath;
    }
}

class XpathSet extends ArrayIterator {}



class Crawler
{
    protected $crawlRule = null;

    protected $source = null;

    public function __construct(Source $source, CrawlRule $crawlRule)
    {
        $this->source = $source;
        $this->crawlRule = $crawlRule;
    }

    protected function normaliseConfig($config)
    {
        $defaultConfig = array(
            'query'     => null,
            'prefix'    => null,
            'suffix'    => null,
            'converter' => null,
        );

        if (is_string($config)) {
            $config = array(
                'query' => $config,
            );
        }

        if (!is_array($config)) {
            throw new Exception('Config must be an array.');
        }

        $config = array_merge($defaultConfig, $config);

        return $config;
    }

    protected function buildText($text, Array $config)
    {
        $text = trim($text);

        if ($config['converter'] instanceof Converter) {
            $text = $config['converter']->getValue($text);
        }

        if ($config['prefix'] !== null) {
            $text = $config['prefix'].$text;
        }

        if ($config['suffix'] !== null) {
            $text .= $config['suffix'];
        }

        return $text;
    }

    protected function curlHtml($url)
    {
	$timeout = 5;
        $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36';

        $ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

	$data = curl_exec($ch);

	curl_close($ch);

	return $data;
    }

    protected function evalSource(Source $source, Array $data)
    {
        if ($source instanceof Data) {
            if (array_key_exists($source, $data)) {
                throw new Exception(sprintf('Index "%s" was not found within the $data array.', $source));
            }

            $source = new Url($data[(string)$source]);
        }

        if ($source instanceof Url) {
            $html = $this->curlHtml((string)$source);

            $source = new Html($html);
        }

        return $source;
    }

    public function getData(DOMXPath $xpath)
    {
        $domElements = $this->crawlRule->getDomElements($xpath);

        switch(true) {
            case $domElements instanceof DOMNodeList:
                return $this->getDataFromPages($domElements, $xpath);
                break;

            case $this->crawlRule instanceof PageGroup:
            case $this->crawlRule instanceof Page:
                return $this->getDataFromPage($domElements, $xpath);
                break;

            default:
                return array();
                break;
        }
    }

    public function getDataFromPages(DOMNodeList $pages, DOMXPath $xpath)
    {
        $data = array();

        foreach ($pages as $page) {
            array_push($data, $this->getDataFromPage($page, $xpath));
        }

        return $data;
    }

    public function getDataFromPage(DOMElement $page, DOMXPath $xpath)
    {
        $data = array();

        if ($page === null) {
            return $data;
        }

        foreach ($this->crawlRule->getMapping() as $key => $config) {
            $config = $this->normaliseConfig($config);

            $hit = $xpath->query($config['query'], $page);

            if ($hit->length <= 0) {
                continue;
            }

            if ($hit->length === 1) {
                $data[$key] = $this->buildText($hit->item(0)->textContent, $config);
                continue;
            }

            $data[$key] = array();

            foreach ($hit as $entry) {
                array_push($data[$key], $this->buildText($entry->textContent, $config));
            }
        }

        /* new crawler detected → crawl next subpage */
        if ($this->crawlRule->getCrawler() instanceof Crawler) {
            $data = $this->crawlRule->getCrawler()->crawl($data);
        }

        return $data;
    }

    public function crawl(Array $data = array())
    {
        $source = clone $this->source;

        $source = $this->evalSource($source, $data);

        if (!$source instanceof Html) {
            throw new Exception('Source must be an instance of Html.');
        }

        $doc = new DOMDocument();

        libxml_use_internal_errors(true);
        $doc->loadHTML($source);
        libxml_clear_errors();

        $xpath = new DOMXpath($doc);

        return array_merge($data, $this->getData($xpath));
    }
}

