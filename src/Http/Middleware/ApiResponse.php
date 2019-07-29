<?php

namespace Shovel\Http\Middleware;

use Closure;
use Shovel\Http;
use Commons\When;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse implements Http
{
    /**
     * Acceptable response classes that will be handled.
     *
     * @var array
     */
    private $acceptedResponses = [
        \Illuminate\Http\Response::class,
        \Illuminate\Http\JsonResponse::class,
        \Illuminate\Routing\ResponseFactory::class,
    ];

    /**
     * Handle the response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure  $next
     * @param string[] ...$options
     * @return \Illuminate\Http\Response
     */
    public function handle($request, Closure $next, ...$options)
    {
        $response = $next($request);
        $response = When::isTrue($this->shouldBuild($request, $response), function () use ($response, $options) {
            $this->beforeResponding($response);
            return $this->buildPayload($response, ...$options);
        }, $response);

        return $response;
    }

    /**
     * Mutate the response keys before the payload is pushed to the client.
     * This allows changing the casing (or anything else) of each key in the
     * payload before it is forwarded to the requesting client.
     *
     * @param string $key
     * @return string|mixed
     */
    protected function keyMutator($key)
    {
        return $key;
    }

    /**
     * Construct the response payload.
     *
     * @param \Illuminate\Http\Response $response
     * @param string[] ...$options
     * @return \Illuminate\Http\Response
     */
    private function buildPayload($response, ...$options)
    {
        $metaTag = $options[0] ?? 'meta';
        $dataTag = $options[1] ?? 'data';
        $pageTag = $options[2] ?? 'pagination';

        $payload = $this->getMetaBlock($response, $metaTag);

        if ($response->content()) {
            if ($this->isPaginated($response)) {
                $payload[$metaTag][$pageTag] = $this->getPaginationBlock($response->original);
                $payload[$dataTag] = $response->original->items();
            } elseif ($this->isPaginatedCollection($response)) {
                $payload[$metaTag][$pageTag] = $this->getPaginationBlock($response->original->resource);
                $payload[$dataTag] = $response->original->resource->items();
            } else {
                $payload[$dataTag] = json_decode($response->content());
            }
        }

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $response->setContent(json_encode($payload));
        } else {
            $response->setContent($payload);
        }

        return $response;
    }

    /**
     * Returns a string defining whether or not the response is successful.
     *
     * @param int $code
     * @return string
     */
    private function getStatus(int $code)
    {
        $range = substr($code, 0, 1);

        if (in_array($range, [4, 5])) {
            return 'error';
        }

        return 'success';
    }

    /**
     * Returns the text representation of the HTTP status code.
     *
     * @param int $code
     * @return string
     */
    private function getStatusMessage(int $code)
    {
        return self::CODES[$code] ?? 'Unknown';
    }

    /**
     * Returns true if the response is a paginated object.
     *
     * @param \Illuminate\Http\Response $response
     * @return bool
     */
    private function isPaginated($response)
    {
        return $response->original instanceof LengthAwarePaginator;
    }

    /**
     * Returns true if the response is a paginated collection.
     *
     * @param \Illuminate\Http\Response $response
     * @return bool
     */
    private function isPaginatedCollection($response)
    {
        return isset($response->original->resource) &&
               $response->original->resource instanceof LengthAwarePaginator;
    }

    /**
     * Constructs and returns the meta object.
     *
     * @param \Illuminate\Http\Response $response
     * @param string $metaTag
     * @return array
     */
    private function getMetaBlock($response, $metaTag)
    {
        $payload = [
            $metaTag => [
               'code'    => $response->status(),
               'status'  => $this->getStatus($response->status()),
               'message' => $this->getStatusMessage($response->status()),
             ]
         ];

        if (isset($response->additionalMeta)) {
            $payload[$metaTag] = array_merge($payload[$metaTag], $response->additionalMeta);
        }

        return $payload;
    }

    /**
     * Constructs and returns the pagination object.
     *
     * @param \Illuminate\Http\Response $response
     * @return array
     */
    private function getPaginationBlock($paginator)
    {
        return [
            'records'  => $paginator->total(),
            'page'     => $paginator->currentPage(),
            'pages'    => $paginator->lastPage(),
            'limit'    => intval($paginator->perPage()),
        ];
    }

    /**
     * Determine if the response should be built.
     *
     * @param \Illuminate\Http\Response $response
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    private function shouldBuild($request, $response)
    {
        return in_array(get_class($response), $this->acceptedResponses);
    }
}
