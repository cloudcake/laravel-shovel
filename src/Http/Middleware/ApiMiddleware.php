<?php

namespace Shovel\Http\Middleware;

use Closure;

class ApiMiddleware
{
    /**
     * Mutate keys.
     *
     * @param array $payload
     * @return array
     */
    protected function mutateKeys(array $data)
    {
        $payload = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->mutateKeys($value);
            } elseif ($value instanceof \Illuminate\Http\Resources\Json\Resource) {
                $value = $this->mutateKeys($value->toArray(null));
            }

            $payload[$this->mutateKey($key)] = $value;
        }

        return $payload;
    }
}
