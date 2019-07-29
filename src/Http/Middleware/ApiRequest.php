<?php

namespace Shovel\Http\Middleware;

use Closure;

class ApiRequest
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
     * Mutate the request key after the payload has been received. This allows
     * changing the casing (or anything else) of each key in the payload before
     * it is forwarded to the rest of the Laravel Application.
     *
     * @param string $key
     * @return string|mixed
     */
    protected function keyMutator($key)
    {
        return $key;
    }
}
