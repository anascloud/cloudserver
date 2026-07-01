<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject',             // Product name
        'deadline',             // Product code
        'company',     // Actual price of the product
        'service',       // Selling price of the product
        'description',      // Product description (nullable)
        'contact',         // Product category
        'source',            // Brand name
        'type',             // uom of the product
        'status',        // Path to the thumbnail image
    ];    


    /**
     * User
     * 
     * Get User Uploaded By Product
     *
     * @return array Products
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
