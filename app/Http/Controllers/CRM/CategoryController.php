<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;
use App\Models\Category;
use App\Repositories\ResponseRepository;
use Illuminate\Support\Facades\DB;

class CategoryController extends ApiCrudController
{
    protected $dbTable = 'categories';

    protected $resource = 'Category';

    protected $resourcePlural = 'Categories';

    protected $fields = ['name', 'description', 'parent_id'];

    protected $searchable = ['name', 'description'];

    protected $searchOnList = false;

    protected $indexPerPage = 10;

    protected $rules = [
        'name' => 'required',
        'parent_id' => 'nullable',
    ];

    protected $writeResponse = 'legacy';

    protected $destroyResponse = 'standard';

    protected $listMessage = 'Category List Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Category Details Fetched Successfully!',
        'store' => 'New Category Created Successfully!',
        'update' => 'Category Updated Successfully.',
        'destroy' => 'Category Deleted Successfully!',
        'search' => 'Category Search Results Fetched Successfully!',
        'not_found' => 'Category Not Found',
    ];

    protected function query()
    {
        if ($this->listMode === 'index') {
            return DB::table('categories as a')
                ->select('a.id', 'a.name', 'a.description', 'a.parent_id', 'b.name as parent_name')
                ->leftJoin('categories as b', 'a.parent_id', '=', 'b.id');
        }

        return DB::table('categories');
    }

    public function show($id)
    {
        try {
            $data = Category::with('parent')->find($id);

            if (is_null($data)) {
                return ResponseRepository::ResponseError(null, 'Category Not Found', 404);
            }

            return ResponseRepository::ResponseSuccess([
                'id' => $data->id,
                'name' => $data->name,
                'description' => $data->description,
                'parent_id' => $data->parent_id,
                'parent_name' => $data->parent ? $data->parent->name : null,
            ], 'Category Details Fetched Successfully!');
        } catch (\Throwable $e) {
            return ResponseRepository::ResponseError(null, $e->getMessage(), 500);
        }
    }
}
