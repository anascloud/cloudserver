<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'fm_countries';

    protected $fillable = [
        'country_name',
        'country_code',
        'date_format',
        'time_format',
        'time_zone',
    ];

    protected $casts = [
        'created_date' => 'datetime',
        'updated_date' => 'datetime',
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'countryName' => $this->country_name,
            'countryCode' => $this->country_code,
            'dateFormat' => $this->date_format,
            'timeFormat' => $this->time_format,
            'timeZone' => $this->time_zone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
