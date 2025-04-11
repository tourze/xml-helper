# XML Helper

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/xml-helper.svg?style=flat-square)](https://packagist.org/packages/tourze/xml-helper)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/xml-helper.svg?style=flat-square)](https://packagist.org/packages/tourze/xml-helper)
[![License](https://img.shields.io/packagist/l/tourze/xml-helper.svg?style=flat-square)](https://packagist.org/packages/tourze/xml-helper)

一个轻量级的 PHP XML 处理库，提供方便的方法用于 XML 字符串和 PHP 数组之间的相互转换。

## 功能特点

- XML 字符串转换为 PHP 数组
- PHP 数组转换为 XML 字符串
- 支持 CDATA 处理特殊字符
- XML 字符串清理，移除无效字符
- 简单直观的 API
- 可自定义根节点和子节点名称
- 支持 XML 属性
- 特殊布尔值处理

## 环境要求

- PHP >= 8.1
- ext-simplexml
- ext-libxml

## 安装

通过 Composer 安装:

```bash
composer require tourze/xml-helper
```

## 快速开始

### 引入命名空间

```php
use Tourze\XML\XML;
```

### XML 转数组

```php
$xml = '<xml><name><![CDATA[张三]]></name><age>25</age></xml>';
$array = XML::parse($xml);

// 结果:
// [
//     'name' => '张三',
//     'age' => '25'
// ]
```

### 数组转 XML

```php
$array = [
    'name' => '张三',
    'age' => 25
];

$xml = XML::build($array);
// 结果: <xml><name><![CDATA[张三]]></name><age>25</age></xml>
```

## 详细文档

### XML::parse(string $xml): array

将 XML 字符串解析为 PHP 数组。

```php
$xml = '<xml><user><name>李四</name><age>30</age></user></xml>';
$array = XML::parse($xml);
```

### XML::build(array $data, string $root = 'xml', string $item = 'item', $attr = '', $id = 'id', $cdata = true, $listKey = [], $specialBool = false): string

将 PHP 数组转换为 XML 字符串。

参数说明:
- `$data`: 要转换的数组
- `$root`: 根元素名称 (默认: 'xml')
- `$item`: 数字索引项的默认名称 (默认: 'item')
- `$attr`: 根元素的 XML 属性 (字符串或数组)
- `$id`: 数字键的属性名 (默认: 'id')
- `$cdata`: 是否将字符串值包装在 CDATA 中 (默认: true)
- `$listKey`: 要格式化为平面列表的键数组
- `$specialBool`: 是否将布尔值输出为 'true'/'false' 字符串 (默认: false)

```php
// 自定义根元素
$xml = XML::build($array, 'root');

// 带属性
$xml = XML::build($array, 'xml', 'item', ['version' => '1.0', 'encoding' => 'UTF-8']);

// 处理包含嵌套对象的列表
$listData = [
    'products' => [
        ['name' => '产品1', 'price' => 100],
        ['name' => '产品2', 'price' => 200]
    ]
];
$xml = XML::build($listData, 'root', 'item', '', 'id', true, ['products']);
```

### XML::cdata(string $string): string

将字符串包装在 CDATA 标签中。

```php
$cdata = XML::cdata('特殊字符 & < >');
// 结果: <![CDATA[特殊字符 & < >]]>
```

### XML::sanitize(string $xml): string

从字符串中删除无效的 XML 字符。

```php
$safeXml = XML::sanitize($potentiallyInvalidXml);
```

## 使用示例

### 处理 API 响应

```php
// 解析 XML API 响应
$xmlResponse = getAPIResponse(); // 返回 XML 字符串
$data = XML::parse($xmlResponse);

// 将数据作为 PHP 数组处理
$processedData = processData($data);

// 如果需要，转换回 XML
$xmlOut = XML::build($processedData);
```

### 处理复杂结构

```php
$data = [
    'header' => [
        'version' => '1.0',
        'encoding' => 'UTF-8'
    ],
    'body' => [
        'user' => [
            'name' => '李四',
            'email' => 'lisi@example.com',
            'roles' => [
                'role' => ['admin', 'editor']
            ]
        ],
        'status' => true
    ]
];

$xml = XML::build($data, 'message', 'item', '', 'id', true, [], true);
```

## 贡献

欢迎贡献！请随时提交 Pull Request。

## 测试

```bash
composer test
```

## 许可证

该软件包是根据 MIT 许可证发布的开源软件。有关更多信息，请参阅 [LICENSE](LICENSE) 文件。
