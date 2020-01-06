# SimpleXML Parser

A simple XML Parser which parses an XML into a normalized array based on SimpleXMLElement.

## Requirements

* PHP >= 7.2
    * Extensions
        * json
        * simplexml
        * libxml
* Composer >= 1.5

## Installation

Use composer to install and use this package in your project.

Install them with

```bash
composer require "dmx/simple-xml-parser"
```

and you are ready to go!

### Usage

*Example:*
```php

$myXMLContent = <<<XML
<xml>
    <foo>
        <bar awesome="true">this is a example</bar>
    </foo>
</xml>
XML;

$myContent = (new DMX\SimpleXML\Parser($myXMLContent))->toArray();
```


## Development - Getting Started

See the [CONTRIBUTING](CONTRIBUTING.md) file.

## Changelog

See the [CHANGELOG](CHANGELOG.md) file.

## License

See the [LICENSE](LICENSE.md) file.
