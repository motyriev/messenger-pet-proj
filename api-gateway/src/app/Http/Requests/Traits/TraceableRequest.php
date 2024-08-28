<?php

namespace App\Http\Requests\Traits;

trait TraceableRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'traceId' => $this->header('X-Trace-Id'),
        ]);
    }
}