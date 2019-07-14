<?php

namespace Shovel\Http\Middleware;

use Closure;
use Commons\When;
use Illuminate\Http\Response;

class ApiResponse
{
    public function handle($request, Closure $next, ...$options)
    {
        $response = $next($request);
        $response = When::isTrue($response instanceof Response, $this->buildPayload(), $response);

        $this->beforeResponding($response);

        return $response;
    }

    protected function beforeResponding($response)
    {
        return $response;
    }

    private function buildPayload($response)
    {
        $payload = [
            'meta' => [
               'code'    => $response->status(),
               'status'  => $this->getStatus($response->status()),
               'message' => $this->getStatusMessage($response->status())
             ],
             'data' => $response->content(),
         ];

        When::isTrue(!$payload['data'], function () use (&$payload) {
            unset($payload['data']);
        });

        $response->setcontent($payload);

        return $response;
    }

    private function getStatus($code)
    {
        $range = substr($code, 0, 1);

        switch ($range) {
           case 4:
           case 5:
              return 'error';
           default:
              return 'success';
        }
    }

    private function getStatusMessage($code)
    {
        switch ($code) {
          case 100: return 'Continue';
          case 101: return 'Switching Protocols';
          case 200: return 'OK';
          case 201: return 'Created';
          case 202: return 'Accepted';
          case 203: return 'Non-Authoritative Information';
          case 204: return 'No Content';
          case 205: return 'Reset Content';
          case 206: return 'Partial Content';
          case 300: return 'Multiple Choices';
          case 301: return 'Moved Permanently';
          case 302: return 'Moved Temporarily';
          case 303: return 'See Other';
          case 304: return 'Not Modified';
          case 305: return 'Use Proxy';
          case 400: return 'Bad Request';
          case 401: return 'Unauthorized';
          case 402: return 'Payment Required';
          case 403: return 'Forbidden';
          case 404: return 'Not Found';
          case 405: return 'Method Not Allowed';
          case 406: return 'Not Acceptable';
          case 407: return 'Proxy Authentication Required';
          case 408: return 'Request Time-out';
          case 409: return 'Conflict';
          case 410: return 'Gone';
          case 411: return 'Length Required';
          case 412: return 'Precondition Failed';
          case 413: return 'Request Entity Too Large';
          case 414: return 'Request-URI Too Large';
          case 415: return 'Unsupported Media Type';
          case 500: return 'Internal Server Error';
          case 501: return 'Not Implemented';
          case 502: return 'Bad Gateway';
          case 503: return 'Service Unavailable';
          case 504: return 'Gateway Time-out';
          case 505: return 'HTTP Version not supported';
          default: return 'unknown';
      }
    }
}
