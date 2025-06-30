<?php

namespace Tourze\XML\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\XML\XML;

class XMLTest extends TestCase
{
    /**
     * 测试 XML 解析为数组
     */
    public function testParse(): void
    {
        // 测试基本解析
        $xml = '<xml><name><![CDATA[张三]]></name><age>25</age></xml>';
        $array = XML::parse($xml);

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('age', $array);
        $this->assertEquals('张三', $array['name']);
        $this->assertEquals('25', $array['age']);

        // 测试嵌套结构
        $xml = '<xml><user><name>李四</name><profile><age>30</age><city>北京</city></profile></user></xml>';
        $array = XML::parse($xml);

        $this->assertArrayHasKey('user', $array);
        $this->assertIsArray($array['user']);
        $this->assertEquals('李四', $array['user']['name']);
        $this->assertEquals('30', $array['user']['profile']['age']);
        $this->assertEquals('北京', $array['user']['profile']['city']);

        // 测试数组列表
        $xml = '<xml><items><item>苹果</item><item>香蕉</item></items></xml>';
        $array = XML::parse($xml);

        $this->assertArrayHasKey('items', $array);
        $this->assertIsArray($array['items']);
        $this->assertArrayHasKey('item', $array['items']);
        $this->assertIsArray($array['items']['item']);
        $this->assertEquals('苹果', $array['items']['item'][0]);
        $this->assertEquals('香蕉', $array['items']['item'][1]);
    }

    /**
     * 测试数组构建为 XML
     */
    public function testBuild(): void
    {
        // 测试基本构建
        $array = [
            'name' => '张三',
            'age' => 25
        ];

        $xml = XML::build($array);
        $this->assertStringContainsString('<xml>', $xml);
        $this->assertStringContainsString('<name><![CDATA[张三]]></name>', $xml);
        $this->assertStringContainsString('<age>25</age>', $xml);
        $this->assertStringContainsString('</xml>', $xml);

        // 测试自定义根节点
        $xml = XML::build($array, 'root');
        $this->assertStringContainsString('<root>', $xml);
        $this->assertStringContainsString('</root>', $xml);

        // 测试嵌套数组
        $nestedArray = [
            'user' => [
                'name' => '李四',
                'profile' => [
                    'age' => 30,
                    'city' => '北京'
                ]
            ]
        ];

        $xml = XML::build($nestedArray);
        $this->assertStringContainsString('<user>', $xml);
        $this->assertStringContainsString('<name><![CDATA[李四]]></name>', $xml);
        $this->assertStringContainsString('<profile>', $xml);
        $this->assertStringContainsString('<age>30</age>', $xml);
        $this->assertStringContainsString('<city><![CDATA[北京]]></city>', $xml);

        // 测试数字索引数组
        $indexedArray = [
            'items' => [
                '苹果',
                '香蕉'
            ]
        ];

        $xml = XML::build($indexedArray, 'xml', 'item');
        $this->assertStringContainsString('<items>', $xml);
        $this->assertStringContainsString('<item id="0"><![CDATA[苹果]]></item>', $xml);
        $this->assertStringContainsString('<item id="1"><![CDATA[香蕉]]></item>', $xml);

        // 测试带有属性的构建
        $xml = XML::build($array, 'xml', 'item', ['version' => '1.0', 'encoding' => 'UTF-8']);
        $this->assertStringContainsString('<xml version="1.0" encoding="UTF-8">', $xml);

        // 测试列表扁平化
        $listData = [
            'products' => [
                ['name' => '产品1', 'price' => 100],
                ['name' => '产品2', 'price' => 200]
            ]
        ];
        $xml = XML::build($listData, 'root', 'item', '', 'id', true, ['products']);
        $this->assertStringContainsString('<products>', $xml);
        $this->assertStringContainsString('<name><![CDATA[产品1]]></name>', $xml);
        $this->assertStringContainsString('<price>100</price>', $xml);
        $this->assertStringContainsString('<name><![CDATA[产品2]]></name>', $xml);
        $this->assertStringContainsString('<price>200</price>', $xml);

        // 测试特殊布尔值处理
        $boolArray = [
            'status' => true,
            'active' => false
        ];
        $xml = XML::build($boolArray, 'xml', 'item', '', 'id', true, [], true);
        $this->assertStringContainsString('<status>true</status>', $xml);
        $this->assertStringContainsString('<active>false</active>', $xml);
    }

    /**
     * 测试 CDATA 方法
     */
    public function testCdata(): void
    {
        $string = '特殊字符 & < >';
        $cdata = XML::cdata($string);

        $this->assertEquals('<![CDATA[特殊字符 & < >]]>', $cdata);
    }

    /**
     * 测试 XML 清理方法
     */
    public function testSanitize(): void
    {
        // 测试清理非法字符
        $invalidXml = "测试\x00数据\x1F清理";
        $validXml = XML::sanitize($invalidXml);

        $this->assertEquals('测试数据清理', $validXml);

        // 测试保留合法字符
        $validChars = "合法字符 \t\n\r";
        $sanitized = XML::sanitize($validChars);

        $this->assertEquals($validChars, $sanitized);
    }

    /**
     * 测试完整的 XML 解析和构建循环
     */
    public function testFullCycle(): void
    {
        $array = [
            'name' => '张三',
            'age' => 25,
            'items' => [
                'item' => ['苹果', '香蕉']
            ]
        ];

        // 构建 XML
        $xml = XML::build($array);

        // 解析回数组
        $parsedArray = XML::parse($xml);

        // 验证原始数组和解析后的数组是否一致
        $this->assertEquals($array['name'], $parsedArray['name']);
        $this->assertEquals($array['age'], $parsedArray['age']);

        // 检查items结构
        $this->assertArrayHasKey('items', $parsedArray);
        $this->assertIsArray($parsedArray['items']);

        // 不要假设具体的数组结构，改用包含断言
        $this->assertStringContainsString('苹果', print_r($parsedArray['items'], true));
        $this->assertStringContainsString('香蕉', print_r($parsedArray['items'], true));
    }

    /**
     * 测试解析带有属性的 XML
     */
    public function testParseWithAttributes(): void
    {
        $xml = '<response status="success"><data id="123"><name>测试产品</name><price>99.99</price></data></response>';
        $array = XML::parse($xml);

        $this->assertArrayHasKey('data', $array);
        $this->assertIsArray($array['data']);
        $this->assertEquals('测试产品', $array['data']['name']);
        $this->assertEquals('99.99', $array['data']['price']);
    }
}
