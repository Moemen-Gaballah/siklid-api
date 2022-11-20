<?php

declare(strict_types=1);

namespace App\Tests\Unit\Foundation\Http;

use App\Foundation\Http\Request as Sut;
use App\Foundation\Util\RequestUtil;
use App\Tests\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @psalm-suppress MissingConstructor - we don't need constructor
 */
class RequestTest extends TestCase
{
    private RequestStack $requestStack;

    private RequestUtil $util;

    public function setUp(): void
    {
        parent::setUp();

        $this->requestStack = new RequestStack();
        $this->util = new RequestUtil($this->json);
    }

    /**
     * @test
     */
    public function request_get_current_request(): void
    {
        $this->requestStack->push(new Request());
        $this->requestStack->push(new Request());
        $current = new Request();
        $this->requestStack->push($current);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertSame($current, $sut->request());
    }

    /**
     * @test
     */
    public function all_get_request_content_if_request_is_json(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('json');
        $request->method('getContent')->willReturn('{"foo":"bar"}');
        $request->request = new InputBag();
        $this->requestStack->push($request);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertSame(['foo' => 'bar'], $sut->all());
    }

    /**
     * @test
     */
    public function all_calls_underlying_request__all_if_request_is_not_json(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('html');
        $request->method('getContent')->willReturn('[]');
        $request->request = new InputBag(['foo' => 'bar']);
        $this->requestStack->push($request);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertSame(['foo' => 'bar'], $sut->all());
    }

    /**
     * @test
     */
    public function is_json_returns_true_if_content_type_is_json(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('json');
        $this->requestStack->push($request);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertTrue($sut->isJson());
    }

    /**
     * @test
     */
    public function is_json_returns_false_if_content_type_is_not_json(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('html');
        $this->requestStack->push($request);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertFalse($sut->isJson());
    }

    /**
     * @test
     */
    public function form_input_returns_the_same_as_all(): void
    {
        $request = $this->createMock(Request::class);
        $request->method('getContentType')->willReturn('json');
        $request->method('getContent')->willReturn('{"foo":"bar"}');
        $request->request = new InputBag();
        $this->requestStack->push($request);
        $sut = new Sut($this->requestStack, $this->util);

        $this->assertSame($sut->all(), $sut->formInput());
    }

    /**
     * @test
     */
    public function get_returns_get_variables(): void
    {
        $sut = new Sut($this->requestStack, $this->util);
        $this->requestStack->push(new Request(['foo' => 'bar']));

        $actual = $sut->get('foo');

        $this->assertSame('bar', $actual);
    }

    /**
     * @test
     */
    public function get_returns_post_variables(): void
    {
        $sut = new Sut($this->requestStack, $this->util);
        $this->requestStack->push(new Request([], ['foo' => 'bar']));

        $actual = $sut->get('foo');

        $this->assertSame('bar', $actual);
    }

    /**
     * @test
     */
    public function get_can_return_a_default_value(): void
    {
        $sut = new Sut($this->requestStack, $this->util);
        $this->requestStack->push(new Request());

        $actual = $sut->get('foo', 'bar');

        $this->assertSame('bar', $actual);
    }
}
