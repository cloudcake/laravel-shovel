<?php

namespace Shovel\Tests\Unit;

use Illuminate\Http\Response as ResponseClass;
use Illuminate\Support\Facades\Response;
use Shovel\Tests\TestCase;

class ModelResponseTest extends TestCase
{
    public function test_single_model()
    {
        $model = \Shovel\Tests\Models\Sample::first();

        $result = Response::shovel($model);

        $this->assertTrue($result instanceof ResponseClass);

        $content = json_decode($result->content(), true);

        $this->assertNotNull($content);

        $this->assertArrayHasKey('meta', $content);
        $this->assertArrayHasKey('data', $content);

        $this->assertArrayHasKey('status', $content['meta']);
        $this->assertArrayHasKey('message', $content['meta']);
        $this->assertArrayHasKey('code', $content['meta']);

        $this->assertNotNull($content['data']);

        $this->assertTrue($content['data']['id'] === $model->id);
        $this->assertTrue($content['data']['name'] === $model->name);
        $this->assertTrue($content['data']['description'] === $model->description);
    }

    public function test_paginated_models()
    {
        $model = \Shovel\Tests\Models\Sample::paginate();

        $result = Response::shovel($model);

        $this->assertTrue($result instanceof ResponseClass);

        $content = json_decode($result->content(), true);

        $this->assertNotNull($content);

        $this->assertArrayHasKey('meta', $content);
        $this->assertArrayHasKey('data', $content);

        $this->assertArrayHasKey('status', $content['meta']);
        $this->assertArrayHasKey('message', $content['meta']);
        $this->assertArrayHasKey('code', $content['meta']);
        $this->assertArrayHasKey('pagination', $content['meta']);

        $this->assertNotNull($content['data']);

        $this->assertTrue(count($content['data']) == $model->count());
    }

    public function test_collected_model()
    {
        $model = \Shovel\Tests\Models\Sample::get();

        $result = Response::shovel($model);

        $this->assertTrue($result instanceof ResponseClass);

        $content = json_decode($result->content(), true);

        $this->assertNotNull($content);

        $this->assertArrayHasKey('meta', $content);
        $this->assertArrayHasKey('data', $content);

        $this->assertArrayHasKey('status', $content['meta']);
        $this->assertArrayHasKey('message', $content['meta']);
        $this->assertArrayHasKey('code', $content['meta']);
        $this->assertArrayNotHasKey('pagination', $content['meta']);
    }

    public function test_single_resource_model()
    {
        $model = \Shovel\Tests\Models\Sample::first();

        $resource = new \Shovel\Tests\Resources\SampleResource($model);

        $result = Response::shovel($resource);

        $this->assertTrue($result instanceof ResponseClass);

        $content = json_decode($result->content(), true);

        $this->assertNotNull($content);

        $this->assertArrayHasKey('meta', $content);
        $this->assertArrayHasKey('data', $content);

        $this->assertArrayHasKey('status', $content['meta']);
        $this->assertArrayHasKey('message', $content['meta']);
        $this->assertArrayHasKey('code', $content['meta']);

        $this->assertNotNull($content['data']);

        $this->assertTrue($content['data']['id'] === $model->id);
        $this->assertTrue($content['data']['name'] === $model->name);
        $this->assertTrue($content['data']['description'] === $model->description);
    }

    public function test_paginated_resource_models()
    {
        $model = \Shovel\Tests\Models\Sample::paginate();

        $resource = \Shovel\Tests\Resources\SampleResource::collection($model);

        $result = Response::shovel($model);

        $this->assertTrue($result instanceof ResponseClass);

        $content = json_decode($result->content(), true);

        $this->assertNotNull($content);

        $this->assertArrayHasKey('meta', $content);
        $this->assertArrayHasKey('data', $content);

        $this->assertArrayHasKey('status', $content['meta']);
        $this->assertArrayHasKey('message', $content['meta']);
        $this->assertArrayHasKey('code', $content['meta']);
        $this->assertArrayHasKey('pagination', $content['meta']);

        $this->assertNotNull($content['data']);

        $this->assertTrue(count($content['data']) == $model->count());
    }

    public function test_pagination_matches_records()
    {
        $count = \Shovel\Tests\Models\Sample::count();
        $model = \Shovel\Tests\Models\Sample::paginate();

        $result = Response::shovel($model);

        $content = json_decode($result->content(), true);

        $this->assertTrue($content['meta']['pagination']['records'] == $count);
    }
}
