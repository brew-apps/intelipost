<?php

namespace Brew\Intelipost\Models;

use Illuminate\Database\Eloquent\Model;

class IntelipostLog extends Model
{
    protected $fillable = [
        'endpoint',
        'request_data',
        'response_data',
        'status_code',
        'execution_time',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];
}
