<?php

namespace Tourze\XML;

/**
 * Class XML.
 */
class XML
{
    /**
     * XML to array.
     */
    public static function parse(string $xml): array
    {
        return self::normalize(simplexml_load_string(self::sanitize($xml), 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_NOCDATA | LIBXML_NOBLANKS));
    }

    /**
     * XML encode.
     *
     * @param string $root
     * @param string $item
     * @param string $attr
     * @param string $id
     * @param array $listKey 临时起的参数 ，这个key的数据如果为数组，数组的对象全在一层
     * @return string
     */
    public static function build(
        $data,
        string $root = 'xml',
        string $item = 'item',
        $attr = '',
        $id = 'id',
        $cdata = true,
        $listKey = [],
        $specialBool = false,
    ) {
        if (is_array($attr)) {
            $_attr = [];

            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }

            $attr = implode(' ', $_attr);
        }

        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml .= self::data2Xml($data, $item, $id, $cdata, $listKey, $specialBool);
        $xml .= "</{$root}>";

        return $xml;
    }

    /**
     * Build CDATA.
     *
     * @param string $string
     * @return string
     */
    public static function cdata($string)
    {
        return sprintf('<![CDATA[%s]]>', $string);
    }

    /**
     * Object to array.
     *
     * @param \SimpleXMLElement $obj
     * @return array
     */
    protected static function normalize($obj)
    {
        $result = null;

        if (is_object($obj)) {
            $obj = (array) $obj;
        }

        if (is_array($obj)) {
            foreach ($obj as $key => $value) {
                $res = self::normalize($value);
                if (('@attributes' === $key) && $key) {
                    $result = $res; // @codeCoverageIgnore
                } else {
                    $result[$key] = $res;
                }
            }
        } else {
            $result = $obj;
        }

        return $result;
    }

    /**
     * Array to XML.
     *
     * @param array $data
     * @param string $item
     * @param string $id
     * @return string
     */
    protected static function data2Xml($data, string $item = 'item', string $id = 'id', $cdata = true, $listKey = [], $specialBool = false)
    {
        $xml = $attr = '';

        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }
            if (in_array($key, $listKey) && is_array($val) || is_object($val)) {
                foreach ($val as $valItem) {
                    $xml .= "<{$key}{$attr}>";
                    $xml .= self::data2Xml((array) $valItem, $item, $id, $cdata, $listKey, $specialBool);
                    $xml .= "</{$key}>";
                }
            } else {
                $xml .= "<{$key}{$attr}>";
                if (is_array($val) || is_object($val)) {
                    $xml .= self::data2Xml((array) $val, $item, $id, $cdata, $listKey, $specialBool);
                } else {
                    if (is_numeric($val) || false === $cdata) {
                        if (is_bool($val) && true === $specialBool) {
                            $xml .= ($val ? 'true' : 'false');
                        } else {
                            $xml .= $val;
                        }
                    } else {
                        if (is_bool($val) && true === $specialBool) {
                            $xml .= ($val ? 'true' : 'false');
                        } else {
                            $xml .= self::cdata($val);
                        }
                    }
                }

                $xml .= "</{$key}>";
            }
        }

        return $xml;
    }

    /**
     * Delete invalid characters in XML.
     *
     * @see https://www.w3.org/TR/2008/REC-xml-20081126/#charsets - XML charset range
     * @see http://php.net/manual/en/regexp.reference.escape.php - escape in UTF-8 mode
     * @param string $xml
     * @return string
     */
    public static function sanitize($xml)
    {
        return preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]+/u', '', $xml);
    }
}
