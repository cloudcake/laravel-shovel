<?php

namespace Shovel;

use ArrayObject;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Shovel extends ArrayObject implements HttpStatusCodes
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
    private $responseInstance;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setFlags(ArrayObject::STD_PROP_LIST | ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * Get the response instance.
     *
     * @return \Illuminate\Http\Response
     */
    public function responseInstance()
    {
        return $this->responseInstance;
    }

    /**
     * Set and parse the response data.
     *
     * @return \Illuminate\Http\Response
     */
    public function provideData($data, $status_code = 200)
    {
        $this->meta['code'] = $status_code;

        if ($this->isSuccessfulResponse()) {
            $this->meta['status'] = 'success';
            $this->meta['message'] = self::STATUS_CODES[$status_code] ?? 'Invalid Status Code';

            if (is_null($data)) {
                unset($this->data);
            } else {
                $this->data = $data;

                if ($this->isPaginatedResource()) {
                    $this->meta['pagination'] = [
                      'records'  => $data->total(),
                      'page'     => $data->currentPage(),
                      'pages'    => $data->lastPage(),
                      'limit'    => intval($data->perPage()),
                    ];

                    if (config('shovel.includePaginationLinks', false)) {
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
            }
        } else {
            $this->meta['status'] = 'error';
            $this->meta['message'] = $data ? $data : (self::STATUS_CODES[$status_code] ?? 'Invalid Status Code');
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
    public function withError($error, $status_code = 422)
    {
        $this->provideData($error, $status_code);

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
        $this->responseInstance = response($this, $this->meta['code']);

        return $this->responseInstance;
    }
}
