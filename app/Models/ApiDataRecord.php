<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiDataRecord extends Model
{
    protected $fillable = [
        'api_configuration_id',
        'external_id',
        'payload_data',
        'response_mapping',
        'is_imported',
    ];

    protected function casts(): array
    {
        return [
            'payload_data' => 'array',
            'response_mapping' => 'array',
            'is_imported' => 'boolean',
        ];
    }

    public function apiConfiguration()
    {
        return $this->belongsTo(ApiConfiguration::class);
    }
}
