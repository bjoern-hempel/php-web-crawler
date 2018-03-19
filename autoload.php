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

/* build lib root dir */
$libDir = dirname(__FILE__).'/lib/Ixno/WebCrawler';

/* require all needed classes */
require_once $libDir.'/Crawler.php';

require_once $libDir.'/Source/Source.php';
require_once $libDir.'/Source/File.php';
require_once $libDir.'/Source/Html.php';
require_once $libDir.'/Source/Url.php';
require_once $libDir.'/Source/XpathSection.php';
require_once $libDir.'/Source/XpathSections.php';

require_once $libDir.'/Output/Output.php';
require_once $libDir.'/Output/Field.php';
require_once $libDir.'/Output/Group.php';

require_once $libDir.'/Query/Query.php';
require_once $libDir.'/Query/XpathTextnode.php';
require_once $libDir.'/Query/XpathTextnodes.php';

require_once $libDir.'/Converter/Converter.php';
require_once $libDir.'/Converter/DateParser.php';
require_once $libDir.'/Converter/PregReplace.php';
require_once $libDir.'/Converter/Trim.php';
