<?php

namespace Shovel\Http\Middleware;

use Closure;
use Shovel\HTTP;
use ArrayObject;
use Commons\When;
use JsonSerializable;
use Illuminate\Http\Response;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\ResponseFactory;

class ApiResponse implements HTTP
{
    public function handle($request, Closure $next, ...$options)
    {
        $response = $next($request);
        $response = When::isTrue($this->shouldBeBuilt($response), function () use ($response, $options) {
            $this->beforeResponding($response);
            return $this->buildPayload($response, ...$options);
        }, $response);

        return $response;
    }

    protected function beforeResponding($response)
    {
        return $response;
    }

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

        $response->setContent($payload);

        return $response;
    }

    private function getStatus($code)
    {
        $range = substr($code, 0, 1);

        if (in_array($range, [4, 5])) {
            return 'error';
        }

        return 'success';
    }

    private function getStatusMessage($code)
    {
        return self::CODES[$code] ?? 'Unknown';
    }

    private function shouldBeBuilt($response)
    {
        return ($response->status() != 500) && (
            is_null($response->content()) ||
            $response->original instanceof Arrayable ||
            $response->original instanceof Jsonable ||
            $response->original instanceof ArrayObject ||
            $response->original instanceof JsonSerializable ||
            is_array($response->original)
        );
    }

    private function isPaginated($response)
    {
        return $response->original instanceof LengthAwarePaginator;
    }

    private function isPaginatedCollection($response)
    {
        return isset($response->original->resource) &&
               $response->original->resource instanceof LengthAwarePaginator;
    }

    private function getMetaBlock($response, $metaTag)
    {
        $payload = [
            $metaTag => [
               'status'  => $this->getStatus($response->status()),
               'message' => $this->getStatusMessage($response->status()),
               'code'    => $response->status(),
             ]
         ];

        if (isset($response->additionalMeta)) {
            $payload[$metaTag] = array_merge($payload[$metaTag], $response->additionalMeta);
        }

        return $payload;
    }

    private function getPaginationBlock($paginator)
    {
        return [
            'records'  => $paginator->total(),
            'page'     => $paginator->currentPage(),
            'pages'    => $paginator->lastPage(),
            'limit'    => intval($paginator->perPage()),
        ];
    }
}
