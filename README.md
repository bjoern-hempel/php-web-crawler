# WebCrawler

This php class allows you to crawl recursively a given webpage and collect some data from it. The final representation will be the json format for further processing.

## How to use

### Basic usage (single webpage)

```
<?php

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

It returns:

```
{
    "title": "Kraftstoffpreise geben leicht nach",
    "subtitle": "Preis für Brent-Öl bei 65 Dollar",
    "category": "Verkehr",
    "date": "07.03.2018",
    "text": [
        "Die Kraftstoffpreise in Deutschland sind im Vergleich zur vergangenen Woche leicht gesunken. Wie die aktuelle Auswertung des ADAC zeigt, kostet ein Liter Super E10 im Tagesmittel 1,324 Euro – ein Minus von 0,6 Cent gegenüber der Vorwoche. Der Dieselpreis fiel um 0,5 Cent und kostet derzeit im Schnitt 1,179 Euro je Liter. Auch Rohöl ist wieder etwas billiger geworden: Der Preis für ein Barrel der Sorte Brent liegt derzeit bei gut 65 Dollar.",
        "Der ADAC empfiehlt den Autofahrern, die täglichen Preisschwankungen an den Tankstellen zu nutzen. Am preiswertesten ist Tanken am späten Nachmittag und abends. Zudem gibt es oft erhebliche Preisunterschiede zwischen den Tankstationen. Einen schnellen Überblick über die aktuellen Spritpreise an den deutschen Tankstellen liefert die Smartphone-App „ADAC Spritpreise“. Ausführliche Informationen gibt es zudem unter adac.de/tanken."
    ]
}
```

### Basic usage with xpath group (single webpage)

```
<?php

require_once('vendor/ixno/webcrawler/lib/Ixno/WebCrawler/Crawler.php');

use Ixno\WebCrawler\Crawler;
use Ixno\WebCrawler\Page;
use Ixno\WebCrawler\XpathSet;
use Ixno\WebCrawler\Url;

$url = 'https://www.page.tld';

$crawler = new Crawler(
    new Url($url),
    new PageGroup(
        new Xpath('/html/body/section[1]/div/div[2]/div/div/article'),
        new Page(
            new XpathSet(
                array(
                    'title' => 'h1[1]',
                    'subtitle' => 'p[contains(concat(" ", normalize-space(@class), " "), " box-intro ")][1]',
                    'category' => 'span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[2]',
                    'date' => 'span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[3]',
                    'text' => 'div[contains(concat(" ", normalize-space(@class), " "), " box-content-grid ")][1]/p',
                )
            )
        )
    )
);

$data = $crawler->crawl();

print json_encode($data);
```

It returns the same json format as the basic example given before.

### Basic usage with xpath group (from given html file)

```
<?php

require_once('vendor/ixno/webcrawler/lib/Ixno/WebCrawler/Crawler.php');

use Ixno\WebCrawler\Crawler;
use Ixno\WebCrawler\Page;
use Ixno\WebCrawler\XpathSet;
use Ixno\WebCrawler\Html;

$html = file_get_contents('file.html');

$crawler = new Crawler(
    new Html($url),
    new PageGroup(
        new Xpath('/html/body/section[1]/div/div[2]/div/div/article'),
        new Page(
            new XpathSet(
                array(
                    'title' => 'h1[1]',
                    'subtitle' => 'p[contains(concat(" ", normalize-space(@class), " "), " box-intro ")][1]',
                    'category' => 'span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[2]',
                    'date' => 'span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[3]',
                    'text' => 'div[contains(concat(" ", normalize-space(@class), " "), " box-content-grid ")][1]/p',
                )
            )
        )
    )
);

$data = $crawler->crawl();

print json_encode($data);
```

It returns the same json format as the basic example given before.

### More complex example (recursive crawling)

```
<?php

require_once('vendor/ixno/webcrawler/lib/Ixno/WebCrawler/Crawler.php');

use Ixno\WebCrawler\Crawler;

use Ixno\WebCrawler\Page;
use Ixno\WebCrawler\PageGroup;
use Ixno\WebCrawler\PageList;

use Ixno\WebCrawler\Xpath;
use Ixno\WebCrawler\XpathSet;

use Ixno\WebCrawler\Data;
use Ixno\WebCrawler\Url;

$url = 'https://www.domain.tld/page.html';

$crawler = new Crawler(
    new Url($url),
    new PageList(
        new Xpath('/html/body/section[1]/div/div[2]/div/div/article'),
        new Page(
            new XpathSet(
                array(
                    'link' => array(
                        'query'  => 'p[1]/a[1][span[contains(text(), \'Mehr\')]]/@href',
                        'prefix' => 'https://presse.adac.de',
                    ),
                )
            )
        ),
        new Crawler(
            new Data('link'),
            new PageGroup(
                new Xpath('/html/body/section[1]/div/div[2]/div/div/article'),
                new Page(
                    new XpathSet(
                        array(
                            'title' => 'h1[1]',
                            'subtitle' => 'p[contains(concat(" ", normalize-space(@class), " "), " box-intro ")][1]',
                            'category' => 'span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[2]',
                            'date' => 'span[contains(concat(" ", normalize-space(@class), " "), " box-date ")][1]/text()[3]',
                            'text' => 'div[contains(concat(" ", normalize-space(@class), " "), " box-content-grid ")][1]/p',
                        )
                    )
                )
            )
        )
    )
);

$data = $crawler->crawl();

print json_encode($data);

exit;
```

It returns:

```
[
    {
        "link": "https://www.domain.tld/folder/subpage.html",
        "title": "Kraftstoffpreise geben leicht nach",
        "subtitle": "Preis für Brent-Öl bei 65 Dollar",
        "category": "Verkehr",
        "date": "07.03.2018",
        "text": [
            "Die Kraftstoffpreise in Deutschland sind im Vergleich zur vergangenen Woche leicht gesunken. Wie die aktuelle Auswertung des ADAC zeigt, kostet ein Liter Super E10 im Tagesmittel 1,324 Euro – ein Minus von 0,6 Cent gegenüber der Vorwoche. Der Dieselpreis fiel um 0,5 Cent und kostet derzeit im Schnitt 1,179 Euro je Liter. Auch Rohöl ist wieder etwas billiger geworden: Der Preis für ein Barrel der Sorte Brent liegt derzeit bei gut 65 Dollar.",
            "Der ADAC empfiehlt den Autofahrern, die täglichen Preisschwankungen an den Tankstellen zu nutzen. Am preiswertesten ist Tanken am späten Nachmittag und abends. Zudem gibt es oft erhebliche Preisunterschiede zwischen den Tankstationen. Einen schnellen Überblick über die aktuellen Spritpreise an den deutschen Tankstellen liefert die Smartphone-App „ADAC Spritpreise“. Ausführliche Informationen gibt es zudem unter adac.de/tanken."
        ]
    },
    ...
]
```
