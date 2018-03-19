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

namespace Ixno\WebCrawler\Query;

use Ixno\WebCrawler\Converter\Converter;
use Ixno\WebCrawler\Output\Output;
use DOMXPath;
use DOMNode;

abstract class Value
{
    protected $value = null;

    protected $queries = array();

    protected $outputs = array();

    protected $converters = array();

    public function __construct()
    {
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {
            if (is_string($parameter)) {
                $this->value = $parameter;
                continue;
            }

            if ($parameter instanceof Value) {
                array_push($this->queries, $parameter);
                continue;
            }

            if ($parameter instanceof Output) {
                array_push($this->outputs, $parameter);
                continue;
            }

            if ($parameter instanceof Converter) {
                array_push($this->converters, $parameter);
                continue;
            }
        }
    }

    protected function applyConverters($value)
    {
        foreach ($this->converters as $converter) {
            $value = $converter->getValue($value);
        }

        return $value;
    }

    public function __toString()
    {
        return $this->value;
    }

    abstract public function parse(DOMXPath $xpath, DOMNode $node = null);
}
