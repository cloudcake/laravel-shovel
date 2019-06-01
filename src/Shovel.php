<?php

namespace Shovel;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Shovel implements HttpStatusCodes
{
    /**
     * Meta data.
     *
     * @var array
     */
    public $meta = [];

    /**
     * Object data.
     *
     * @var mixed
     */
    public $data = null;

    /**
     * Response instance.
     *
     * @var \Illuminate\Http\Response
     */
    private $response;

    /**
     * Config repository instance.
     *
     * @var \Illuminate\Config\Repository
     */
    private $config;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = (object) config('shovel', [
            'includePaginationLinks' => false,
            'omitEmptyObject'        => false,
            'omitEmptyArray'         => false,
        ]);
    }

    /**
     * Get the response instance.
     *
     * @return \Illuminate\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set and parse the response data.
     *
     * @return \Illuminate\Http\Response
     */
    public function provideData($data, $code)
    {
        $this->meta['code'] = $code ?? $data;

        if (!$this->isSuccessfulResponse()) {
            $this->meta['status'] = 'error';
            $this->meta['message'] = $data ? $data : (self::STATUS_CODES[$code] ?? 'Invalid Status Code');

            return $this->registerResponse();
        }

        $this->meta['status'] = 'success';
        $this->meta['message'] = self::STATUS_CODES[$code] ?? 'Invalid Status Code';

        $this->data = $data;

        if ($this->isPaginatedResource()) {
            $this->meta['pagination'] = [
              'records'  => $data->total(),
              'page'     => $data->currentPage(),
              'pages'    => $data->lastPage(),
              'limit'    => intval($data->perPage()),
            ];

            if ($this->config->includePaginationLinks) {
                $this->meta['pagination']['links'] = [
                  'current'  => $data->url($data->currentPage()),
                  'previous' => $data->previousPageUrl(),
                  'next'     => $data->nextPageUrl(),
                  'last'     => $data->url($data->lastPage()),
                ];
            }

            $this->data = $this->data->items();
        }

        if (method_exists($this->data, 'toArray')) {
            $this->data = $this->data->toArray(request());
        }

        return $this->registerResponse();
    }

    /**
     * Apply extra meta data.
     *
     * @return \Illuminate\Http\Response
     */
    public function withMeta($key, $data)
    {
        $dotNotation = new \Adbar\Dot($this->meta);
        $dotNotation->set($key, $data);

        $this->meta = $dotNotation->all();

        return $this->registerResponse();
    }

    /**
     * Apply custom error data.
     *
     * @return \Illuminate\Http\Response
     */
    public function withError($error, $code = 422)
    {
        $this->provideData($error, $code);

        return $this->registerResponse();
    }

    /**
     * Apply custom meta message.
     *
     * @return \Illuminate\Http\Response
     */
    public function withMessage($message = '')
    {
        $this->meta['message'] = $message;

        return $this->registerResponse();
    }

    /**
     * Returns true if data object is paginated.
     *
     * @return \Illuminate\Http\Response
     */
    private function isPaginatedResource()
    {
        return ($this->data instanceof ResourceCollection &&
                method_exists($this->data->resource, 'currentPage')) ||
                method_exists($this->data, 'currentPage');
    }

    /**
     * Returns true if the status code is in the successful range.
     *
     * @return bool
     */
    private function isSuccessfulResponse()
    {
        return in_array((int) substr($this->meta['code'], 0, 1), [2, 3]);
    }

    /**
     * Apply data updates.
     *
     * @return \Illuminate\Http\Response
     */
    private function registerResponse()
    {
        $response = [
            'meta' => $this->meta,
            'data' => $this->data,
        ];

        if (empty($this->data) && ($this->config->omitEmptyObject || $this->config->omitEmptyArray)) {
            unset($response['data']);
        }

        $this->response = response($response, $this->meta['code']);

        return $this->response;
    }
}
