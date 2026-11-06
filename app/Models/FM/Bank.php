<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'fm_banks';
    protected $fillable = ["bank_name","bank_website","swift_code","routing_number","contact_number"];
}
