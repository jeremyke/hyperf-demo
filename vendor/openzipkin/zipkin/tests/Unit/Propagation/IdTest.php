<?php

namespace ZipkinTests\Unit\Propagation;

use PHPUnit\Framework\TestCase;
use function Zipkin\Propagation\Id\generateNextId;
use function Zipkin\Propagation\Id\generateTraceIdWith128bits;
use function Zipkin\Propagation\Id\isValidSpanId;
use function Zipkin\Propagation\Id\isValidTraceId;

final class IdTest extends TestCase
{
    public function testNextIdSuccess()
    {
        $nextId = generateNextId();
        $this->assertTrue(ctype_xdigit($nextId));
        $this->assertEquals(16, strlen($nextId));
    }

    public function testTraceIdWith128bitsSuccess()
    {
        $nextId = generateTraceIdWith128bits();
        $this->assertTrue(ctype_xdigit($nextId));
        $this->assertEquals(32, strlen($nextId));
    }

    /**
     * @dataProvider spanIdsDataProvider
     */
    public function testIsValidSpanIdSuccess($spanId, $isValid)
    {
        $this->assertEquals($isValid, isValidSpanId($spanId));
    }

    public function spanIdsDataProvider()
    {
        return [
            ['', false],
            ['1', true],
            ['50d1e105a060618', true],
            ['050d1e105a060618', true],
            ['g50d1e105a060618', false],
            ['050d1e105a060618a', false],
        ];
    }

    /**
     * @dataProvider traceIdsDataProvider
     */
    public function testIsValidTraceIdSuccess($traceId, $isValid)
    {
        $this->assertEquals($isValid, isValidTraceId($traceId));
    }

    public function traceIdsDataProvider()
    {
        return [
            ['', false],
            ['1', true],
            ['050d1e105a060618', true],
            ['g50d1e105a060618', false],
            ['050d1e105a060618050d1e105a060618', true],
            ['050d1e105a060618g50d1e105a060618', false],
            ['050d1e105a060618050d1e105a060618a', false],
        ];
    }
}
