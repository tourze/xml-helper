# XML Helper

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/xml-helper.svg?style=flat-square)](https://packagist.org/packages/tourze/xml-helper)
[![Latest Version](https://img.shields.io/packagist/v/tourze/xml-helper.svg?style=flat-square)](https://packagist.org/packages/tourze/xml-helper)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/xml-helper.svg?style=flat-square)](https://packagist.org/packages/tourze/xml-helper)
[![License](https://img.shields.io/packagist/l/tourze/xml-helper.svg?style=flat-square)](https://packagist.org/packages/tourze/xml-helper)
[![Build Status](https://img.shields.io/github/actions/workflow/status/owner/repo/ci.yml?style=flat-square)](https://github.com/owner/repo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/owner/repo?style=flat-square)](https://codecov.io/gh/owner/repo)

A lightweight PHP library for simple XML parsing and generation. 
Provides convenient methods to convert between XML strings and PHP arrays.

## Table of Contents

- [Installation](#installation)
- [Requirements](#requirements)
- [Features](#features)
- [Quick Start](#quick-start)
  - [Import the namespace](#import-the-namespace)
  - [Convert XML to Array](#convert-xml-to-array)
  - [Convert Array to XML](#convert-array-to-xml)
- [Documentation](#documentation)
  - [XML::parse()](#xmlparse)
  - [XML::build()](#xmlbuild)
  - [XML::cdata()](#xmlcdata)
  - [XML::sanitize()](#xmlsanitize)
- [Advanced Usage](#advanced-usage)
  - [Custom Configuration](#custom-configuration)
  - [Performance Optimization](#performance-optimization)
- [Example Use Cases](#example-use-cases)
  - [Working with API Responses](#working-with-api-responses)
  - [Handling Complex Structures](#handling-complex-structures)
- [Contributing](#contributing)
- [Testing](#testing)
- [License](#license)

## Installation

```bash
composer require tourze/xml-helper
```

## Requirements

- PHP >= 8.1
- ext-simplexml
- ext-libxml

## Features

- Convert XML strings to PHP arrays
- Convert PHP arrays to XML strings
- CDATA support for handling special characters
- XML sanitization to remove invalid characters
- Simple and intuitive API
- Customizable root and child node names
- Support for XML attributes
- Special boolean value handling

## Quick Start

### Import the namespace

```php
use Tourze\XML\XML;
```

### Convert XML to Array

```php
$xml = '<xml><name><![CDATA[John Doe]]></name><age>25</age></xml>';
$array = XML::parse($xml);

// Result:
// [
//     'name' => 'John Doe',
//     'age' => '25'
// ]
```

### Convert Array to XML

```php
$array = [
    'name' => 'John Doe',
    'age' => 25
];

$xml = XML::build($array);
// Result: <xml><name><![CDATA[John Doe]]></name><age>25</age></xml>
```

## Documentation

### XML::parse()

Parses an XML string into a PHP array.

```php
$xml = '<xml><user><name>Alice</name><age>30</age></user></xml>';
$array = XML::parse($xml);
```

### XML::build()

Converts a PHP array to an XML string.

Parameters:
- `$data`: The array to convert
- `$root`: Root element name (default: 'xml')
- `$item`: Default name for numerically indexed items (default: 'item')
- `$attr`: XML attributes for the root element (string or array)
- `$id`: Attribute name for numeric keys (default: 'id')
- `$cdata`: Whether to wrap string values in CDATA (default: true)
- `$listKey`: Array of keys to format as a flat list
- `$specialBool`: Whether to output boolean values as 'true'/'false' strings (default: false)

```php
// With custom root element
$xml = XML::build($array, 'root');

// With attributes
$xml = XML::build($array, 'xml', 'item', ['version' => '1.0', 'encoding' => 'UTF-8']);

// Handling lists with nested objects
$listData = [
    'products' => [
        ['name' => 'Product 1', 'price' => 100],
        ['name' => 'Product 2', 'price' => 200]
    ]
];
$xml = XML::build(
    $listData, 
    'root', 
    'item', 
    '', 
    'id', 
    true, 
    ['products']
);
```

### XML::cdata()

Wraps a string in CDATA tags.

```php
$cdata = XML::cdata('Special characters & < >');
// Result: <![CDATA[Special characters & < >]]>
```

### XML::sanitize()

Removes invalid XML characters from a string.

```php
$safeXml = XML::sanitize($potentiallyInvalidXml);
```

## Advanced Usage

### Custom Configuration

```php
// Advanced build options
$xml = XML::build(
    $data,
    $root = 'customRoot',
    $item = 'customItem', 
    $attr = ['version' => '2.0'],
    $id = 'identifier',
    $cdata = false,
    $listKey = ['products', 'categories'],
    $specialBool = true
);
```

### Performance Optimization

```php
// For large datasets, disable CDATA if not needed
$xml = XML::build($largeArray, 'root', 'item', '', 'id', false);

// Pre-sanitize XML for better performance
$cleanXml = XML::sanitize($rawXmlString);
$array = XML::parse($cleanXml);
```

## Example Use Cases

### Working with API Responses

```php
// Parse an XML API response
$xmlResponse = getAPIResponse(); // Returns XML string
$data = XML::parse($xmlResponse);

// Process the data as a PHP array
$processedData = processData($data);

// Convert back to XML if needed
$xmlOut = XML::build($processedData);
```

### Handling Complex Structures

```php
$data = [
    'header' => [
        'version' => '1.0',
        'encoding' => 'UTF-8'
    ],
    'body' => [
        'user' => [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'roles' => [
                'role' => ['admin', 'editor']
            ]
        ],
        'status' => true
    ]
];

$xml = XML::build($data, 'message', 'item', '', 'id', true, [], true);
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Testing

```bash
./vendor/bin/phpunit packages/xml-helper/tests
```

## License

This package is open-sourced software licensed under the MIT license. See the [LICENSE](LICENSE) file for more information.
