<?php

function shovel($data = null, $status_code = 200)
{
    $shovel = app('shovel');
    $shovel->provideData($data, $status_code);

    return $shovel->responseInstance();
}
