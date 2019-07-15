<?php

namespace App\Http\Middleware;

use Illuminate\Http\Response;
use Shovel\Http\Middleware\ApiResponse as Middleware;

class ApiResponse extends Middleware
{
    /**
     * Allow transforming of response before it is returned.
     *
     * @param \Illuminate\Http\Response $response
     * @return \Illuminate\Http\Response
     */
    protected function beforeResponding(Response $response)
    {
        //
    }
}
