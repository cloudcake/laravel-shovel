<?php

function shovel($data = null, $code = 200)
{
    return response()->shovel($data, $code);
}
