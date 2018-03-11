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

/**
 * Class to store and retrieve the version of WebCrawler
 *
 * @version 1.0 (2018-03-12)
 * @author  Björn Hempel <bjoern@hempel.li>
 */
class Version
{
    /**
     * Current WebCrawler Version
     */
    const VERSION = '1.0.0';

    /**
     * Compares a WebCrawler version with the current one.
     *
     * @param string $version WebCrawler version to compare.
     *
     * @return int Returns -1 if older, 0 if it is the same, 1 if version
     *             passed as argument is newer.
     */
    public static function compare($version)
    {
        $currentVersion = str_replace(' ', '', strtolower(self::VERSION));
        $version        = str_replace(' ', '', $version);

        return version_compare($version, $currentVersion);
    }
}

