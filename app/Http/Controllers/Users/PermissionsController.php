<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;

class PermissionsController extends ApiCrudController
{
    protected $dbTable = 'permissions';

    protected $resource = 'Permission';

    protected $resourcePlural = 'Permissions';

    protected $fields = [
        'name',
        'module',
        'group',
    ];

    protected $searchable = ['name', 'module', 'group'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'module' => 'required|string|max:255',
        'group' => 'required|string|max:255',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'permissionId';

    protected $listMessage = 'Permission List Fetched Successfully!';

    protected $indexAllMessage = 'All Permissions Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Permission Details Fetched Successfully!',
        'store' => 'New Permission Created Successfully!',
        'update' => 'Permission Updated Successfully!',
        'destroy' => 'Permission Deleted Successfully!',
        'search' => 'Permission Search Results Fetched Successfully!',
        'not_found' => 'Permission Not Found',
    ];

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'name',
                'module',
                'group',
                'created_at',
                'updated_at'
            );
        }
    }
}
