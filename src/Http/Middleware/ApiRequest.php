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
     * Mutate keys.
     *
     * @param array $payload
     * @return array
     */
    private function mutateKeys(array $data)
    {
        $payload = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->mutateKeys($value);
            }

            $payload[$this->mutateKey($key)] = $value;
        }

        return $payload;
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
