<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salesorder extends Model
{
    use HasFactory;

    protected $table = 'sales_orders';

    protected $fillable = [
        'type',
        'title',
        'customerId',
        'quotationId',
        'salesOrderNo',
        'currencyId',
        'delivaryDate',
        'updateStock',
        'company_id',
        'stages',
        'posting_date',
        'shippingStreet',
        'shippingHouse',
        'shippingZip',
        'shippingState',
        'shippingCity',
        'shippingCountryId',
        'billingStreet',
        'billingHouse',
        'billingZip',
        'billingState',
        'billingCity',
        'billingCountryId',
        'discountType',
        'applyAmount',
        'discountAmount',
        'subtotal',
        'discount',
        'tax',
        'total',
    ];

    protected $casts = [
        'customerId' => 'integer',
        'quotationId' => 'integer',
        'currencyId' => 'integer',
        'company_id' => 'integer',
        'shippingCountryId' => 'integer',
        'billingCountryId' => 'integer',
        'updateStock' => 'boolean',
        'delivaryDate' => 'date:Y-m-d',
        'posting_date' => 'date:Y-m-d',
        'applyAmount' => 'decimal:2',
        'discountAmount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];
}
