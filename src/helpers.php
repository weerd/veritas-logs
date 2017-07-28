<?php

use Illuminate\Support\Facades\Log;

function read_log_file_contents($path, $maxlength)
{
    if (! file_exists($path)) {
        return null;
    }

    try {
        return file_get_contents($path, false, null, $maxlength);
    } catch (\ErrorException $error) {
        Log::error($error);

        // dump(file_get_contents($path, false, null));

        return file_get_contents($path, false, null);
    }
}
