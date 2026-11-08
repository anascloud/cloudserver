<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\ApiCrudController;
use App\Models\FM\Payment;
use Illuminate\Http\Request;

class PaymentController extends ApiCrudController
{
    protected $model = Payment::class;

    protected $resource = 'Payment';

    protected $resourcePlural = 'Payments';

    protected $searchable = ['payment_no', 'party_name', 'reference_number'];

    protected $filters = ['companyId' => 'company_id'];

    protected $rules = [
        'posting_date' => 'required|date',
        'payment_amount' => 'required|numeric',
    ];

    protected $updateRules = [];

    protected $listFormat = 'compact';

    protected $useRequestAll = true;

    protected $with = ['company'];

    protected function beforeStore(Request $request, array $data)
    {
        $last = $this->query()->orderBy('id', 'DESC')->first();
        $next = $last ? intval(substr($last->payment_no, 4)) + 1 : 1;
        $data['payment_no'] = 'PAY-' . str_pad($next, 6, '0', STR_PAD_LEFT);

        return $data;
    }
}
