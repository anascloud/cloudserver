<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',             // Product name
        'code',             // Product code
        'actualPrice',     // Actual price of the product
        'sellPrice',       // Selling price of the product
        'description',      // Product description (nullable)
        'category',         // Product category
        'brand',            // Brand name
        'unit',             // uom of the product
        'thumbnail',        // Path to the thumbnail image
        'user_id'           // Foreign key for user (if applicable)
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

    // Add New Attribute to get image address
    protected $appends = ['image_url'];
    public function  getImageUrlAttribute(){
        if(is_null($this->image) || $this->image === "")
            return null;
        return url('')."/images/products/".$this->image;
    }
}
