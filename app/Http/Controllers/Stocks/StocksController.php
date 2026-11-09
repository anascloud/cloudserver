<?php

namespace App\Http\Controllers\Stocks;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Support\Facades\DB;

class StocksController extends ApiCrudController
{
    protected $dbTable = 'stocks';

    protected $resource = 'Stock';

    protected $resourcePlural = 'Stocks';

    protected $fields = ['productId', 'warehouseId', 'stockQuantity'];

    protected $searchable = ['products.name', 'stocks.warehouseId'];

    protected $searchOnList = false;

    protected $indexPerPage = 10;

    protected $rules = [
        'productId' => 'required|integer',
        'warehouseId' => 'required|string',
        'stockQuantity' => 'required|integer',
    ];

    protected $writeResponse = 'legacy';

    protected $listMessage = 'Stock List Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Stock Details Fetched Successfully!',
        'store' => 'New Stock Created Successfully!',
        'update' => 'Stock Updated Successfully!',
        'destroy' => 'Stock Deleted Successfully!',
        'search' => 'Stock Search Results Fetched Successfully!',
        'not_found' => 'Stock Not Found',
    ];

    protected function query()
    {
        if (in_array($this->listMode, ['index', 'search'], true)) {
            return DB::table('stocks')
                ->join('products', 'stocks.productId', '=', 'products.id')
                ->select('stocks.*', 'products.name as product_name');
        }

        return DB::table('stocks');
    }
}
