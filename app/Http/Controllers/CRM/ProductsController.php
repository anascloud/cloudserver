<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductsController extends ApiCrudController
{
    protected $dbTable = 'products';

    protected $resource = 'Product';

    protected $resourcePlural = 'Products';

    protected $fields = [
        'name',
        'code',
        'actualPrice',
        'salePrice',
        'description',
        'categoryId',
        'brand',
        'unit',
        'unitCost',
        'status',
        'thumbnail',
        'attributes',
        'user_id',
    ];

    protected $searchable = ['name', 'code', 'description', 'brand'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:100|unique:products,code',
        'actualPrice' => 'required|numeric',
        'salePrice' => 'required|numeric',
        'description' => 'nullable|string|max:1000',
        'categoryId' => 'required|string|max:255',
        'brand' => 'nullable|string|max:255',
        'unit' => 'nullable|string|max:50',
        'unitCost' => 'nullable|numeric',
        'status' => 'nullable|string|max:50',
        'thumbnail' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        'attributes' => 'nullable',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'productId';

    protected $listMessage = 'Product List Fetched Successfully!';

    protected $indexAllMessage = 'All Products Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Product Details Fetched Successfully!',
        'store' => 'New Product Created Successfully!',
        'update' => 'Product Updated Successfully!',
        'destroy' => 'Product Deleted Successfully!',
        'search' => 'Product Search Results Fetched Successfully!',
        'not_found' => 'Product Not Found',
    ];

    /**
     * Override update so the current record is excluded from the
     * `code` uniqueness check. `store()` keeps the plain unique rule.
     */
    public function update(Request $request, $id = null)
    {
        $id = $id ?: $request->input('id');

        $this->rules['code'] = [
            'required',
            'string',
            'max:100',
            Rule::unique('products', 'code')->ignore($id),
        ];

        return parent::update($request, $id);
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'name',
                'code',
                'actualPrice',
                'salePrice',
                'description',
                'categoryId',
                'brand',
                'unit',
                'unitCost',
                'status',
                'thumbnail',
                'user_id',
                'created_at',
                'updated_at'
            );
        }
    }
}
