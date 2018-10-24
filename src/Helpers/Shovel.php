<?php

function shovel($data = null, $status_code = 200)
{
    if ($data) {
        $shovel = app('shovel');
        $shovel->provideData($data, $status_code);

        return $shovel->responseInstance();
    }

    return app('shovel');
}
