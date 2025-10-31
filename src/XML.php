<?php

declare(strict_types=1);

namespace Tourze\XML;

use Tourze\XML\Exception\XmlParseException;

/**
 * Class XML.
 */
class XML
{
    /**
     * XML 转换为数组
     */
    /**
     * @return array<string, mixed>
     */
    public static function parse(string $xml): array
    {
        $sanitized = self::sanitize($xml);
        $result = simplexml_load_string($sanitized, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NOBLANKS);

        if (false === $result) {
            throw new XmlParseException('XML 解析失败，格式不正确');
        }

        $normalized = self::normalize($result);

        if (!is_array($normalized)) {
            throw new XmlParseException('XML 解析失败，格式不正确');
        }

        return $normalized;
    }

    /**
     * 数组编码为 XML
     *
     * @param array $listKey 临时起的参数 ，这个key的数据如果为数组，数组的对象全在一层
     */
    /**
     * @param array<string, string>|string $attr
     * @param array<string> $listKey
     */
    public static function build(
        mixed $data,
        string $root = 'xml',
        string $item = 'item',
        string|array $attr = '',
        string $id = 'id',
        bool $cdata = true,
        array $listKey = [],
        bool $specialBool = false,
    ): string {
        if (is_array($attr)) {
            $_attr = [];

            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }

            $attr = implode(' ', $_attr);
        }

        $attr = trim($attr);
        $attr = ('' === $attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml .= self::data2Xml($data, $item, $id, $cdata, $listKey, $specialBool);
        $xml .= "</{$root}>";

        return $xml;
    }

    /**
     * Build CDATA.
     */
    public static function cdata(string $string): string
    {
        return sprintf('<![CDATA[%s]]>', $string);
    }

    /**
     * 对象转换为数组
     *
     * @param \SimpleXMLElement $obj
     */
    /**
     * @param array<string, mixed>|\SimpleXMLElement|string|false|null $obj
     */
    protected static function normalize(\SimpleXMLElement|array|string|false|null $obj): mixed
    {
        if (is_object($obj)) {
            $obj = (array) $obj;
        }

        if (!is_array($obj)) {
            return $obj;
        }

        return self::normalizeArray($obj);
    }

    /**
     * 递归标准化数组
     */
    /**
     * @param array<string, mixed> $obj
     * @return array<string, mixed>
     */
    private static function normalizeArray(array $obj): array
    {
        $result = [];

        foreach ($obj as $key => $value) {
            $res = self::normalize($value);
            if ('@attributes' === $key) {
                // 如果是属性，合并到结果数组中
                if (is_array($res)) {
                    $result = array_merge($result, $res);
                }
            } else {
                $result[$key] = $res;
            }
        }

        return $result;
    }

    /**
     * Array to XML.
     *
     * @param array $data
     */
    /**
     * @param array<string> $listKey
     */
    protected static function data2Xml(
        mixed $data,
        string $item = 'item',
        string $id = 'id',
        bool $cdata = true,
        array $listKey = [],
        bool $specialBool = false,
    ): string {
        $xml = '';

        foreach ($data as $key => $val) {
            [$processedKey, $attr] = self::processKey($key, $item, $id);

            if (self::isListKey($processedKey, $listKey, $val)) {
                $xml .= self::buildListItems($processedKey, $attr, $val, $item, $id, $cdata, $listKey, $specialBool);
            } else {
                $xml .= self::buildSingleItem($processedKey, $attr, $val, $item, $id, $cdata, $listKey, $specialBool);
            }
        }

        return $xml;
    }

    /**
     * 处理 XML 元素的键
     */
    /**
     * @return array{string, string}
     */
    private static function processKey(string|int $key, string $item, string $id): array
    {
        $attr = '';

        if (is_numeric($key)) {
            if ('' !== $id) {
                $attr = " {$id}=\"{$key}\"";
            }
            $key = $item;
        }

        return [$key, $attr];
    }

    /**
     * 检查键是否在列表键中且值是数组或对象
     */
    /**
     * @param array<string> $listKey
     */
    private static function isListKey(string $key, array $listKey, mixed $val): bool
    {
        return in_array($key, $listKey, true) && (is_array($val) || is_object($val));
    }

    /**
     * 构建列表项 XML
     */
    /**
     * @param array<string> $listKey
     */
    private static function buildListItems(
        string $key,
        string $attr,
        mixed $val,
        string $item,
        string $id,
        bool $cdata,
        array $listKey,
        bool $specialBool,
    ): string {
        $xml = '';

        foreach ($val as $valItem) {
            $xml .= "<{$key}{$attr}>";
            $xml .= self::data2Xml((array) $valItem, $item, $id, $cdata, $listKey, $specialBool);
            $xml .= "</{$key}>";
        }

        return $xml;
    }

    /**
     * 构建单个项 XML
     */
    /**
     * @param array<string> $listKey
     */
    private static function buildSingleItem(
        string $key,
        string $attr,
        mixed $val,
        string $item,
        string $id,
        bool $cdata,
        array $listKey,
        bool $specialBool,
    ): string {
        $xml = "<{$key}{$attr}>";

        if (is_array($val) || is_object($val)) {
            $xml .= self::data2Xml((array) $val, $item, $id, $cdata, $listKey, $specialBool);
        } else {
            $xml .= self::formatValue($val, $cdata, $specialBool);
        }

        $xml .= "</{$key}>";

        return $xml;
    }

    /**
     * 格式化 XML 内容值
     */
    private static function formatValue(mixed $val, bool $cdata, bool $specialBool): string
    {
        if (is_bool($val) && $specialBool) {
            return $val ? 'true' : 'false';
        }

        if (is_numeric($val) || !$cdata) {
            return (string) $val;
        }

        return self::cdata((string) $val);
    }

    /**
     * 删除 XML 中的无效字符
     *
     * @see https://www.w3.org/TR/2008/REC-xml-20081126/#charsets - XML charset range
     * @see http://php.net/manual/en/regexp.reference.escape.php - escape in UTF-8 mode
     */
    public static function sanitize(string $xml): string
    {
        $result = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]+/u', '', $xml);

        return $result ?? '';
    }
}
