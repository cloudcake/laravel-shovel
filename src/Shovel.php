<?php

namespace Shovel;

use ArrayObject;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Shovel extends ArrayObject implements HttpStatusCodes
{
    public $meta = [];
    public $data = null;

    private $responseInstance;

    public function __construct()
    {
        $this->setFlags(ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS);
    }

    public function responseInstance()
    {
        return $this->responseInstance;
    }

    public function provideData($data, $status_code = null)
    {
        $this->meta['code'] = $status_code;

        if ($this->isSuccessfulResponse()) {
            $this->meta['status'] = 'success';

            if (is_null($data)) {
                unset($this->data);
            } else {
                $this->data = $data;

                if ($this->isPaginatedResource()) {
                    $this->meta['pagination'] = [
                      'records'  => $data->total(),
                      'page'     => $data->currentPage(),
                      'pages'    => $data->lastPage(),
                      'limit'    => $data->perPage(),
                    ];

                    $this->data = $this->data->items();
                }

                if (method_exists($this->data, 'toArray')) {
                    $this->data = $this->data->toArray(request());
                }
            }
        } else {
            $this->meta['status'] = 'error';
        }

        $this->meta['message'] = self::STATUS_CODES[$status_code] ?? 'Invalid Status Code';

        $this->registerResponse();
    }

    public function withMeta($key, $data)
    {
        $dotNotation = new \Adbar\Dot($this->meta);
        $dotNotation->set($key, $data);

        $this->meta = $dotNotation->all();

        return $this->registerResponse();
    }

    public function withError($error, $status_code = 422)
    {
        $this->meta['message'] = $error;
        $this->meta['status'] = 'error';
        $this->meta['code'] = $status_code;

        return $this->registerResponse();
    }

    public function withMessage($message = '')
    {
        $this->meta['message'] = $message;

        return $this->registerResponse();
    }

    private function isPaginatedResource()
    {
        return ($this->data instanceof ResourceCollection &&
                method_exists($this->data->resource, 'currentPage')) ||
                method_exists($this->data, 'currentPage');
    }

    private function isSuccessfulResponse()
    {
        return in_array((int) substr($this->meta['code'], 0, 1), [2, 3]);
    }

    private function registerResponse()
    {
        if (!$this->responseInstance) {
            $this->responseInstance = response($this, $this->meta['code']);
        }

        $this->responseInstance->setContent($this);

        return $this->responseInstance;
    }
}
