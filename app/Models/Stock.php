<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = ['product', 'warehouse', 'quantity'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}