<?php
// app/Models/Customer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customer';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'company',
        'street', 'house', 'zip', 'city', 'state', 'country',
        'account_no', 'bank_name',
        'currency_id', 'payment_terms', 'chart_of_account_id',
        'residence_permit_no', 'photo_path', 'cover_photo_path',
        'lead_id', 'status', 'user_id',
    ];

    protected $casts = [
        'country'             => 'integer',
        'currency_id'         => 'integer',
        'payment_terms'       => 'integer',
        'chart_of_account_id' => 'integer',
        'lead_id'             => 'integer',
    ];

    protected $hidden = [];

    // Convenience appends so the React side gets a full URL
    protected $appends = ['photo_url', 'cover_photo_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? url($this->photo_path) : null;
    }

    public function getCoverPhotoUrlAttribute(): ?string
    {
        return $this->cover_photo_path ? url($this->cover_photo_path) : null;
    }
}
