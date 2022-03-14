![brd6/notion-sdk-php](resources/banner.png "brd6/notion-sdk-php")

<p align="center">
    <strong>Notion SDK for PHP</strong>
</p>

<!--
<p align="center">
    <a href="https://github.com/brd6/notion-sdk-php"><img src="https://img.shields.io/badge/source-brd6/notion--sdk--php-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/brd6/notion-sdk-php"><img src="https://img.shields.io/packagist/v/brd6/notion-sdk-php.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/brd6/notion-sdk-php.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/brd6/notion-sdk-php/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/brd6/notion-sdk-php.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/brd6/notion-sdk-php/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/workflow/status/brd6/notion-sdk-php/build/main?style=flat-square&logo=github" alt="Build Status"></a>
    <a href="https://codecov.io/gh/brd6/notion-sdk-php"><img src="https://img.shields.io/codecov/c/gh/brd6/notion-sdk-php?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/brd6/notion-sdk-php"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Fbrd6%2Fnotion-sdk-php%2Fcoverage" alt="Psalm Type Coverage"></a>
</p>
-->

PHP version of the official [NOTION API](https://developers.notion.com). It works the same way as the reference [JavaScript SDK](https://github.com/makenotion/notion-sdk-js) 🎉


## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require brd6/notion-sdk-php
```

## Usage

> Use Notion's [Getting Started Guide](https://developers.notion.com/docs/getting-started) to get set up to use Notion's API.

Import and initialize a client using an **integration token** or an OAuth **access token**.

``` php
use Brd6\NotionSdkPhp\Client;
use Brd6\NotionSdkPhp\ClientOptions;

$options = (new ClientOptions())
    ->setAuth(getenv('NOTION_TOKEN'));

$notion = new Client($options);
```

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).







## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.


