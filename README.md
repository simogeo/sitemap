Simple PHP sitemap class
========================

A PHP sitemap class relying on WGET and SED command tools. Simple & efficient.

How to use it?
--------------

```php
include_once('class.sitemap.php');
$sitemap = new sitemap('http://www.mywebsite.com', './');
$sitemap->generate();
```

Constructor options
--------------
- *$url* : url to crawl
- *$sitemap_path* : physical path to sitemap folder. For exemple '/var/www/website/'. You can get this value by looking at $_SERVER['SCRIPT_FILENAME'] value. It makes you able to generate a sitemap for your own website (in that case you will write the file at the root folder) but also for any website (in that case, you would probably store the file into the current folder './').
- *$working_path* (optional) : by default, use the current path. Could be overrided, for example '/tmp/'
- *$sitemap_name* (optional) : by default, use 'sitemap.xml' value
- *$priority* (optional) : by default take '0.500' value


Caution
--------------

Be sure to have permissions on working/writing folders !!!


MIT LICENSE
--------------

Copyright (c) 2012 Simon Georget

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


