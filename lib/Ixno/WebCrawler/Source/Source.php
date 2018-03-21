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

namespace Ixno\WebCrawler\Source;

use DOMDocument;
use DOMXPath;
use DOMNode;

use Ixno\WebCrawler\Output\Field;
use Ixno\WebCrawler\Output\Output;
use Ixno\WebCrawler\Value\Value;

abstract class Source
{
    protected $source = null;

    protected $outputs = array();

    protected $sources = array();

    public function __construct()
    {
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {

            /* main config */
            if (is_string($parameter) && $this->source === null) {
                $this->addSource($parameter);
                continue;
            }

            /* add Output object */
            if ($parameter instanceof Output) {
                array_push($this->outputs, $parameter);
                continue;
            }

            /* convert Value object to Output object */
            if ($parameter instanceof Value) {
                array_push($this->outputs, new Field($parameter));
                continue;
            }

            if ($parameter instanceof Source) {
                array_push($this->sources, $parameter);
                continue;
            }
        }
    }

    protected function getDOMXPathFromSource()
    {
        $doc = new DOMDocument();

        libxml_use_internal_errors(true);
        $doc->loadHTML($this->source);
        libxml_clear_errors();

        return new DOMXpath($doc);
    }

    protected function doParse(DOMXPath $xpath, DOMNode $node = null, Array $data = array())
    {
        $collectedData = array();

        foreach ($this->outputs as $output) {
            $collectedData = array_merge_recursive($collectedData, $output->parse($xpath, $node));
        }

        foreach ($this->sources as $source) {
            $collectedData = array_merge_recursive($collectedData, $source->parse($xpath, $node, $data));
        }

        return $collectedData;
    }

    public function parse(DOMXPath $xpath = null, DOMNode $node = null, Array $data = array())
    {
        $xpath = $this->getDOMXPathFromSource();

        return $this->doParse($xpath, $node, $data);
    }

    abstract public function addSource($source);
}
