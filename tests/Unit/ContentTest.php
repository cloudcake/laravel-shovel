<?php

namespace Shovel\Tests\Unit;

use Shovel\Tests\TestCase;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as ResponseClass;

class ContentTest extends TestCase
{
    public function test_defaults_success()
    {
        $result = Response::shovel();

        $this->assertTrue($result instanceof ResponseClass);

        $content = json_decode($result->content(), true);

        $this->assertNotNull($content);
        $this->assertArrayNotHasKey('data', $content);
        $this->assertArrayHasKey('meta', $content);
        $this->assertArrayHasKey('status', $content['meta']);
        $this->assertArrayHasKey('message', $content['meta']);
        $this->assertArrayHasKey('code', $content['meta']);

        $this->assertTrue($content['meta']['code'] == 200);
        $this->assertTrue($content['meta']['status'] == 'success');
        $this->assertTrue($content['meta']['message'] == 'OK');
    }

    public function test_defaults_error()
    {
        Request::shovelError();

        $result = Response::shovel();

        $this->assertTrue($result instanceof ResponseClass);

        $content = json_decode($result->content(), true);

        $this->assertNotNull($content);
        $this->assertArrayNotHasKey('data', $content);
        $this->assertArrayHasKey('meta', $content);
        $this->assertArrayHasKey('status', $content['meta']);
        $this->assertArrayHasKey('message', $content['meta']);
        $this->assertArrayHasKey('code', $content['meta']);

        $this->assertTrue($content['meta']['code'] == 422);
        $this->assertTrue($content['meta']['status'] == 'error');
        $this->assertTrue($content['meta']['message'] == 'Unprocessable Entity');
    }

    public function test_custom_error()
    {
        Request::shovelError(404, 'Donno where that is');

        $result = Response::shovel();

        $this->assertTrue($result instanceof ResponseClass);

        $content = json_decode($result->content(), true);

        $this->assertNotNull($content);
        $this->assertArrayNotHasKey('data', $content);
        $this->assertArrayHasKey('meta', $content);
        $this->assertArrayHasKey('status', $content['meta']);
        $this->assertArrayHasKey('message', $content['meta']);
        $this->assertArrayHasKey('code', $content['meta']);

        $this->assertTrue($content['meta']['code'] == 404);
        $this->assertTrue($content['meta']['status'] == 'error');
        $this->assertTrue($content['meta']['message'] == 'Donno where that is');
    }

    public function test_custom_payload()
    {
        $result = Response::shovel([
          'custom' => 'payload'
        ], 201);

        $this->assertTrue($result instanceof ResponseClass);

        $content = json_decode($result->content(), true);

        $this->assertNotNull($content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('meta', $content);
        $this->assertArrayHasKey('status', $content['meta']);
        $this->assertArrayHasKey('message', $content['meta']);
        $this->assertArrayHasKey('code', $content['meta']);

        $this->assertTrue($content['meta']['code'] == 201);
        $this->assertTrue($content['meta']['status'] == 'success');
        $this->assertTrue($content['meta']['message'] == 'Created');
        $this->assertTrue($content['data']['custom'] === 'payload');
    }
}
