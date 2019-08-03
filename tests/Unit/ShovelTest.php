<?php

namespace Shovel\Tests\Unit;

use Shovel\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Shovel\Http\Middleware\ApiResponse;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Pagination\LengthAwarePaginator;

class ShovelTest extends TestCase
{
    public function testResponse()
    {
        $request = new Request;
        $request->headers->set('Accept', 'application/json');

        $response = (new ApiResponse())->handle($request, function ($req) {
            return new Response(['unit' => 'test'], 201);
        });

        $json = json_decode($response->content());

        $this->assertTrue(isset($json->meta));
        $this->assertTrue(isset($json->data));
        $this->assertTrue($json->meta->code === 201);
        $this->assertTrue($json->data->unit === 'test');
    }

    public function testJsonResponse()
    {
        $request = new Request;
        $request->headers->set('Accept', 'application/json');

        $response = (new ApiResponse())->handle($request, function ($req) {
            $response = new JsonResponse();
            $response->setContent('{"unit":"test"}');

            return $response;
        });

        $json = json_decode($response->content());


        $this->assertTrue(isset($json->meta));
        $this->assertTrue(isset($json->data));
        $this->assertTrue($json->meta->code === 200);
        $this->assertTrue($json->data->unit === 'test');
    }

    public function testResource()
    {
        $request = new Request;
        $request->headers->set('Accept', 'application/json');

        $response = (new ApiResponse())->handle($request, function ($req) {
            $response = new Response(new Resource(['unit' => 'test']), 201);

            return $response;
        });

        $json = json_decode($response->content());

        $this->assertTrue(isset($json->meta));
        $this->assertTrue(isset($json->data));
        $this->assertTrue($json->meta->code === 201);
        $this->assertTrue($json->data->unit === 'test');
    }

    public function testResourceCollection()
    {
        $request = new Request;
        $request->headers->set('Accept', 'application/json');

        $response = (new ApiResponse())->handle($request, function ($req) {
            $response = new Response(Resource::collection(collect([['unit' => 'test1'], ['unit' => 'test2']])), 201);

            return $response;
        });

        $json = json_decode($response->content());

        $this->assertTrue(isset($json->meta));
        $this->assertTrue(isset($json->data));
        $this->assertTrue($json->meta->code === 201);
        $this->assertTrue($json->data[0]->unit === 'test1');
        $this->assertTrue($json->data[1]->unit === 'test2');
    }

    public function testPagination()
    {
        $request = new Request;
        $request->headers->set('Accept', 'application/json');

        $response = (new ApiResponse())->handle($request, function ($req) {
            $collection = collect([['unit' => 'test1'], ['unit' => 'test2']]);

            $response = new Response(new LengthAwarePaginator($collection, $collection->count(), 1), 201);

            return $response;
        });

        $json = json_decode($response->content());

        $this->assertTrue(isset($json->meta));
        $this->assertTrue(isset($json->data));
        $this->assertTrue(isset($json->meta->pagination));
        $this->assertTrue($json->meta->pagination->page === 1);
        $this->assertTrue($json->meta->pagination->pages === 2);
        $this->assertTrue($json->meta->code === 201);
        $this->assertTrue($json->data[0]->unit === 'test1');
        $this->assertTrue($json->data[1]->unit === 'test2');
    }

    public function testResourcePagination()
    {
        $request = new Request;
        $request->headers->set('Accept', 'application/json');

        $response = (new ApiResponse())->handle($request, function ($req) {
            $collection = collect([['unit' => 'test1'], ['unit' => 'test2']]);

            $response = new Response(Resource::collection(new LengthAwarePaginator($collection, $collection->count(), 1)), 201);

            return $response;
        });

        $json = json_decode($response->content());

        $this->assertTrue(isset($json->meta));
        $this->assertTrue(isset($json->data));
        $this->assertTrue(isset($json->meta->pagination));
        $this->assertTrue($json->meta->pagination->page === 1);
        $this->assertTrue($json->meta->pagination->pages === 2);
        $this->assertTrue($json->meta->code === 201);
        $this->assertTrue($json->data[0]->unit === 'test1');
        $this->assertTrue($json->data[1]->unit === 'test2');
    }

    public function testCustomizedFieldNames()
    {
        $request = new Request;
        $request->headers->set('Accept', 'application/json');

        $response = (new ApiResponse())->handle($request, function ($req) {
            $response = new Response(null, 400);
            $response->setContent(['unit' => 'test']);

            return $response;
        }, ...['headers', 'body']);

        $json = json_decode($response->content());

        $this->assertTrue(isset($json->headers));
        $this->assertTrue(isset($json->body));
        $this->assertTrue($json->headers->code === 400);
        $this->assertTrue($json->body->unit === 'test');
    }

    public function testCustomizedMeta()
    {
        $request = new Request;
        $request->headers->set('Accept', 'application/json');

        $response = (new ApiResponse())->handle($request, function ($req) {
            $response = new Response(null, 400);
            $response->setContent(['unit' => 'test']);
            $response->withMeta('ballpark', true);
            $response->withMeta('arrays', [1,2,3]);
            return $response;
        }, ...['headers', 'body']);

        $json = json_decode($response->content());

        $this->assertTrue(isset($json->headers));
        $this->assertTrue(isset($json->body));
        $this->assertTrue($json->headers->code === 400);
        $this->assertTrue($json->headers->ballpark === true);
        $this->assertTrue($json->headers->arrays === [1,2,3]);
        $this->assertTrue($json->body->unit === 'test');

        $response = (new ApiResponse())->handle($request, function ($req) {
            $response = new JsonResponse();
            $response->setContent('{"unit":"test"}');
            $response->withMeta('ballpark', true);
            $response->withMeta('arrays', [1,2,3]);
            return $response;
        }, ...['headers', 'body']);

        $json = json_decode($response->content());

        $this->assertTrue(isset($json->headers));
        $this->assertTrue(isset($json->body));
        $this->assertTrue($json->headers->code === 200);
        $this->assertTrue($json->headers->ballpark === true);
        $this->assertTrue($json->headers->arrays === [1,2,3]);
        $this->assertTrue($json->body->unit === 'test');
    }
}
