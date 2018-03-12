# WebCrawler

This php class allows you to crawl recursively a given webpage and collect some data from it. The final representation will be the json format for further processing.

## How to use

### Basic usage (single webpage)

```
require_once('vendor/ixno/webcrawler/lib/Ixno/WebCrawler/Crawler.php');

use Ixno\WebCrawler\Crawler;
use Ixno\WebCrawler\Page;
use Ixno\WebCrawler\XpathSet;
use Ixno\WebCrawler\Url;

$url = 'https://www.page.tld';

$crawler = new Crawler(
    new Url($url),
    new Page(
        new XpathSet(
            array(
                'title' => '/html/body/section[1]/div/div[2]/div/div/article/h1[1]',
                'subtitle' => '/html/body/section[1]/div/div[2]/div/div/article/p[contains(concat(" ", normalize-space(@class), " "), " box-intro ")][1]',
                'category' => '/html/body/section[1]/div/div[2]/div/div/article/span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[2]',
                'date' => '/html/body/section[1]/div/div[2]/div/div/article/span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[3]',
                'text' => '/html/body/section[1]/div/div[2]/div/div/article/div[contains(concat(" ", normalize-space(@class), " "), " box-content-grid ")][1]/p',
            )
        )
    )
);

$data = $crawler->crawl();

print json_encode($data);
```

