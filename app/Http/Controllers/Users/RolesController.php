<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;

class RolesController extends ApiCrudController
{
    protected $dbTable = 'roles';

    protected $resource = 'Role';

    protected $resourcePlural = 'Roles';

    protected $fields = [
        'name',
        'permissions',
    ];

    protected $searchable = ['name'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
        'permissions' => 'nullable|string|max:1000',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'roleId';

    protected $listMessage = 'Role List Fetched Successfully!';

    protected $indexAllMessage = 'All Roles Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Role Details Fetched Successfully!',
        'store' => 'New Role Created Successfully!',
        'update' => 'Role Updated Successfully!',
        'destroy' => 'Role Deleted Successfully!',
        'search' => 'Role Search Results Fetched Successfully!',
        'not_found' => 'Role Not Found',
    ];

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'name',
                'permissions',
                'created_at',
                'updated_at'
            );
        }
    }
}
