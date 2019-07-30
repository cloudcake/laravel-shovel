<?php

namespace Shovel\Http\Middleware;

use Closure;

class ApiRequest extends ApiMiddleware
{
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
    }

    /**
     * Mutate the request keys before the payload is processed by the app.
     *
     * @param string $key
     * @return string|mixed
     */
    protected function mutateKey($key)
    {
        return $key;
    }
}
