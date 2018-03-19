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

namespace Ixno\WebCrawler\Output;

use Ixno\WebCrawler\Query\Query;
use Ixno\WebCrawler\Source\Source;

use DOMXPath;
use DOMNode;

abstract class Output
{
    protected $name = null;

    protected $queries = array();

    protected $outputs = array();

    protected $sources = array();

    public function __construct()
    {
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {
            if (is_string($parameter)) {
                $this->name = $parameter;
                continue;
            }

            if ($parameter instanceof Query) {
                array_push($this->queries, $parameter);
                continue;
            }

            if ($parameter instanceof Output) {
                array_push($this->outputs, $parameter);
                continue;
            }

            if ($parameter instanceof Source) {
                array_push($this->sources, $parameter);
                continue;
            }
        }
    }

    public function getName()
    {
        return $this->name;
    }

    protected function getData($data)
    {
        if ($this->getName() !== null) {
            return array($this->getName() => $data);
        }

        return array($data);
    }

    abstract public function parse(DOMXPath $xpath, DOMNode $node = null);
};

class Group extends Output
{
    public function parse(DOMXPath $xpath, DOMNode $node = null)
    {
        $data = array();

        foreach ($this->outputs as $output) {
            $data = array_merge_recursive($data, $output->parse($xpath, $node));
        }

        foreach ($this->sources as $source) {
            $data = array_merge_recursive($data, $source->parse($xpath, $node));
        }

        return $this->getData($data);
    }
}

class Field extends Output
{
    public function parse(DOMXPath $xpath, DOMNode $node = null)
    {
        if (count($this->queries) === 0) {
            return $this->getData(null);
        }

        if (count($this->queries) === 1) {
            return $this->getData($this->queries[0]->parse($xpath, $node));
        }

        $data = array();

        foreach ($this->queries as $query) {
            array_push($data, $query->parse($xpath, $node));
        }

        return $this->getData($data);
    }
}






namespace Ixno\WebCrawler\Query;

use Ixno\WebCrawler\Output\Output;
use DOMXPath;
use DOMNode;

abstract class Query
{
    protected $xpathQuery = null;

    protected $queries = array();

    protected $outputs = array();

    public function __construct()
    {
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {
            if (is_string($parameter)) {
                $this->xpathQuery = $parameter;
                continue;
            }

            if ($parameter instanceof Query) {
                array_push($this->queries, $parameter);
                continue;
            }

            if ($parameter instanceof Output) {
                array_push($this->outputs, $parameter);
                continue;
            }
        }
    }

    public function __toString()
    {
        return $this->xpathQuery;
    }

    abstract public function parse(DOMXPath $xpath, DOMNode $node = null);
};

class XpathField extends Query
{
    public function parse(DOMXPath $xpath, DOMNode $node = null)
    {
        $domNodeList = $xpath->query($this->xpathQuery, $node);

        if ($domNodeList->length === 0) {
            return null;
        }

        if ($domNodeList->length === 1) {
            return $domNodeList->item(0)->textContent;
        }

        $data = array();

        foreach ($domNodeList as $domNode) {
            array_push($data, $domNode->textContent);
        }

        return $data;
    }
}

class XpathFields extends Query
{
    public function parse(DOMXPath $xpath, DOMNode $node = null)
    {
        $domNodeList = $xpath->query($this->xpathQuery, $node);

        if ($domNodeList->length === 0) {
            return array();
        }

        $data = array();

        foreach ($domNodeList as $domNode) {
            array_push($data, $domNode->textContent);
        }

        return $data;
    }
}