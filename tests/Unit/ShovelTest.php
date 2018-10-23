<?php

namespace Shovel\Tests\Unit;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Shovel\Tests\TestCase;
use Shovel\Tests\Resources\SampleResource;
use Shovel\Tests\Models\Sample;

class ContentTest extends TestCase
{
    public function testEmptyResponse()
    {
        $responseArray = json_decode(Response::shovel()->content(), true);

        $this->assertArrayNotHasKey('data', $responseArray);
        $this->assertArrayHasKey('meta', $responseArray);
        $this->assertArrayHasKey('status', $responseArray['meta']);
        $this->assertArrayHasKey('message', $responseArray['meta']);
        $this->assertArrayHasKey('code', $responseArray['meta']);

        $this->assertTrue($responseArray['meta']['code'] == 200);
        $this->assertTrue($responseArray['meta']['status'] == 'success');
        $this->assertTrue($responseArray['meta']['message'] == 'OK');
    }

    public function testSingleError()
    {
        $responseArray = json_decode(Response::shovel()->withError('This is an error')->content(), true);

        $this->assertArrayNotHasKey('data', $responseArray);
        $this->assertArrayHasKey('status', $responseArray['meta']);
        $this->assertArrayHasKey('message', $responseArray['meta']);
        $this->assertArrayHasKey('code', $responseArray['meta']);

        $this->assertTrue($responseArray['meta']['code'] == 422);
        $this->assertTrue($responseArray['meta']['status'] == 'error');
        $this->assertTrue($responseArray['meta']['message'] == 'This is an error');
    }

    public function testMultipleErrors()
    {
        $errors = [
          'This is the first error',
          'This is the second error'
        ];

        $responseArray = json_decode(Response::shovel()->withError($errors)->content(), true);

        $this->assertTrue(is_array($responseArray['meta']['message']));
        $this->assertTrue($responseArray['meta']['message'][0] == $errors[0]);
        $this->assertTrue($responseArray['meta']['message'][1] == $errors[1]);
    }

    public function testPayloadSingleObject()
    {
        $payload = [
          'name' => 'Foo',
          'type' => 'Bar'
        ];

        $responseArray = json_decode(Response::shovel($payload)->content(), true);

        $this->assertArrayHasKey('meta', $responseArray);
        $this->assertArrayHasKey('status', $responseArray['meta']);
        $this->assertArrayHasKey('message', $responseArray['meta']);
        $this->assertArrayHasKey('code', $responseArray['meta']);
        $this->assertArrayHasKey('data', $responseArray);

        $this->assertTrue($responseArray['meta']['code'] == 200);
        $this->assertTrue($responseArray['meta']['status'] == 'success');
        $this->assertTrue($responseArray['meta']['message'] == 'OK');
        $this->assertTrue($responseArray['data']['name'] == $payload['name']);
        $this->assertTrue($responseArray['data']['type'] == $payload['type']);
    }

    public function testPayloadMultipleObjects()
    {
        $payload = [
          [
            'name' => 'Foo',
            'type' => 'Bar'
          ],
          [
            'name' => 'John',
            'type' => 'Doe'
          ],
        ];

        $responseArray = json_decode(Response::shovel($payload)->content(), true);

        $this->assertArrayHasKey('meta', $responseArray);
        $this->assertArrayHasKey('status', $responseArray['meta']);
        $this->assertArrayHasKey('message', $responseArray['meta']);
        $this->assertArrayHasKey('code', $responseArray['meta']);
        $this->assertArrayHasKey('data', $responseArray);

        $this->assertTrue($responseArray['meta']['code'] == 200);
        $this->assertTrue($responseArray['meta']['status'] == 'success');
        $this->assertTrue($responseArray['meta']['message'] == 'OK');
        $this->assertTrue($responseArray['data'][0]['name'] == $payload[0]['name']);
        $this->assertTrue($responseArray['data'][0]['type'] == $payload[0]['type']);
        $this->assertTrue($responseArray['data'][1]['name'] == $payload[1]['name']);
        $this->assertTrue($responseArray['data'][1]['type'] == $payload[1]['type']);
    }

    public function testPayloadSingleJsonResource()
    {
        $payload = [
            'name' => 'Foo',
            'type' => 'Bar'
        ];

        $responseArray = json_decode(Response::shovel(new SampleResource($payload))->content(), true);

        $this->assertArrayHasKey('meta', $responseArray);
        $this->assertArrayHasKey('status', $responseArray['meta']);
        $this->assertArrayHasKey('message', $responseArray['meta']);
        $this->assertArrayHasKey('code', $responseArray['meta']);
        $this->assertArrayHasKey('data', $responseArray);

        $this->assertTrue($responseArray['meta']['code'] == 200);
        $this->assertTrue($responseArray['meta']['status'] == 'success');
        $this->assertTrue($responseArray['meta']['message'] == 'OK');
        $this->assertTrue($responseArray['data']['name'] == $payload['name']);
        $this->assertTrue($responseArray['data']['type'] == $payload['type']);
    }

    public function testPayloadJsonResourceCollection()
    {
        $payload = collect([
          [
              'name' => 'Foo',
              'type' => 'Bar'
          ],
          [
            'name' => 'John',
            'type' => 'Doe'
          ],
        ]);

        $responseArray = json_decode(Response::shovel(SampleResource::collection($payload))->content(), true);

        $this->assertArrayHasKey('meta', $responseArray);
        $this->assertArrayHasKey('status', $responseArray['meta']);
        $this->assertArrayHasKey('message', $responseArray['meta']);
        $this->assertArrayHasKey('code', $responseArray['meta']);
        $this->assertArrayHasKey('data', $responseArray);

        $this->assertTrue($responseArray['meta']['code'] == 200);
        $this->assertTrue($responseArray['meta']['status'] == 'success');
        $this->assertTrue($responseArray['meta']['message'] == 'OK');
        $this->assertTrue($responseArray['data'][0]['name'] == $payload[0]['name']);
        $this->assertTrue($responseArray['data'][0]['type'] == $payload[0]['type']);
        $this->assertTrue($responseArray['data'][1]['name'] == $payload[1]['name']);
        $this->assertTrue($responseArray['data'][1]['type'] == $payload[1]['type']);
    }

    public function testModelPlainPagination()
    {
        $someModelCount = rand(25, 100);

        for ($i = 0; $i < $someModelCount; $i++) {
            Sample::create([
              'name'        => "Shovel Model #{$i}",
              'description' => 'Shovel Test Model',
            ]);
        }

        $paginatedModels = Sample::paginate();

        $responseArray = json_decode(Response::shovel($paginatedModels)->content(), true);

        $this->assertArrayHasKey('meta', $responseArray);
        $this->assertArrayHasKey('data', $responseArray);
        $this->assertArrayHasKey('status', $responseArray['meta']);
        $this->assertArrayHasKey('message', $responseArray['meta']);
        $this->assertArrayHasKey('code', $responseArray['meta']);

        $this->assertTrue($responseArray['meta']['pagination']['pages'] > 0);
        $this->assertTrue($responseArray['meta']['pagination']['page'] == 1);
        $this->assertTrue($responseArray['meta']['pagination']['records'] == Sample::count());
    }

    public function testModelResourcePagination()
    {
        $someModelCount = rand(25, 100);

        for ($i = 0; $i < $someModelCount; $i++) {
            Sample::create([
            'name'        => "Shovel Model #{$i}",
            'description' => 'Shovel Test Model',
          ]);
        }

        $paginatedModels = Sample::paginate();

        $responseArray = json_decode(Response::shovel(SampleResource::collection($paginatedModels))->content(), true);

        $this->assertArrayHasKey('meta', $responseArray);
        $this->assertArrayHasKey('data', $responseArray);
        $this->assertArrayHasKey('status', $responseArray['meta']);
        $this->assertArrayHasKey('message', $responseArray['meta']);
        $this->assertArrayHasKey('code', $responseArray['meta']);
        $this->assertArrayHasKey('pagination', $responseArray['meta']);

        $this->assertTrue($responseArray['meta']['pagination']['pages'] > 0);
        $this->assertTrue($responseArray['meta']['pagination']['page'] == 1);
        $this->assertTrue($responseArray['meta']['pagination']['records'] == Sample::count());
    }

    public function testWithMessage()
    {
        $payload = [
            'name' => 'Foo',
            'type' => 'Bar'
        ];

        $metaMessage = 'I am a banana.';

        $responseArray = json_decode(Response::shovel($payload)->withMessage($metaMessage)->content(), true);

        $this->assertTrue($responseArray['meta']['message'] == $metaMessage);
    }

    public function testWithMeta()
    {
        $extraMetaKey = 'params';
        $extraMetaData = ['key' => 'value'];

        $metaMessage = 'I am a banana.';

        $responseArray = json_decode(Response::shovel()->withMeta($extraMetaKey, $extraMetaData)->content(), true);

        $this->assertTrue($responseArray['meta']['params'] == $extraMetaData);
    }

    public function testWithMetaDotNotation()
    {
        $extraMetaKey = 'params.and.other.stuff';
        $extraMetaData = ['key' => 'value'];

        $metaMessage = 'I am a banana.';

        $responseArray = json_decode(Response::shovel()->withMeta($extraMetaKey, $extraMetaData)->content(), true);

        $this->assertTrue($responseArray['meta']['params']['and']['other']['stuff'] == $extraMetaData);
    }
}
