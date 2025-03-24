# XML Helper

一个简单但功能强大的 XML 处理工具，支持 XML 和数组之间的相互转换。

A simple yet powerful XML processing tool that supports bidirectional conversion between XML and arrays.

## 功能特点 / Features

- XML 转数组 / XML to Array
- 数组转 XML / Array to XML
- 支持 CDATA / CDATA support
- 支持属性 / Attribute support
- 支持特殊布尔值处理 / Special boolean value handling
- 支持自定义根节点和子节点名称 / Custom root and child node names
- 支持数组列表扁平化 / Array list flattening support

## 安装 / Installation

```bash
composer require tourze/xml-helper
```

## 使用示例 / Usage Examples

### XML 转数组 / XML to Array

```php
use Tourze\XML\XML;

$xml = '<xml><name>test</name><age>18</age></xml>';
$array = XML::parse($xml);
```

### 数组转 XML / Array to XML

```php
use Tourze\XML\XML;

$array = [
    'name' => 'test',
    'age' => 18
];
$xml = XML::build($array);
```

### 带属性的 XML / XML with Attributes

```php
use Tourze\XML\XML;

$array = [
    'item' => [
        ['id' => 1, 'name' => 'test1'],
        ['id' => 2, 'name' => 'test2']
    ]
];
$xml = XML::build($array, 'root', 'item', '', 'id');
```

## 系统要求 / Requirements

- PHP >= 8.1
- ext-simplexml
- ext-libxml

## 许可证 / License

MIT License

## 致谢 / Credits

本包部分代码来自 [easywechat](https://github.com/w7corp/easywechat)，感谢原作者。

Some code in this package is from [easywechat](https://github.com/w7corp/easywechat), thanks to the original author.
