<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $table = 'quotations';

    protected $fillable = [
        'title',
        'quotationNo',
        'customerId',
        'leadId',
        'campaignId',
        'type',
        'status',
        'stage',
        'amount',
        'probability',
        'forecastedRevenue',
        'expiryDate',
        'paymentTerms',
        'deliveryStatus',
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
        'primaryContact',
        'nextStep',
        'description',
    ];

    protected $casts = [
        'customerId' => 'integer',
        'leadId' => 'integer',
        'campaignId' => 'integer',
        'shippingCountryId' => 'integer',
        'billingCountryId' => 'integer',
        'amount' => 'decimal:2',
        'probability' => 'integer',
        'forecastedRevenue' => 'decimal:2',
        'expiryDate' => 'date:Y-m-d',
    ];
}
