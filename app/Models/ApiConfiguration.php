<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiConfiguration extends Model
{
    protected $fillable = [
        'name',
        'endpoint',
        'method',
        'headers',
        'query_params',
        'body_payload',
        'response_mapping',
        'sample_response',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'query_params' => 'array',
            'body_payload' => 'array',
            'response_mapping' => 'array',
            'sample_response' => 'array',
            'is_active' => 'boolean',
        ];
    }
    
    public function dataRecords()
    {
        return $this->hasMany(ApiDataRecord::class);
    }
}
