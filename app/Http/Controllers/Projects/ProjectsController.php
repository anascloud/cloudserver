<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;

class ProjectsController extends ApiCrudController
{
    protected $dbTable = 'projects';

    protected $resource = 'Project';

    protected $resourcePlural = 'Projects';

    protected $fields = ['title', 'category', 'description', 'image', 'link'];

    protected $searchable = ['title', 'category', 'description'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'title' => 'required|string|max:255',
        'category' => 'required|string|max:255',
        'description' => 'nullable|string|max:5000',
        'image' => 'nullable|string|max:500',
        'link' => 'nullable|string|max:500',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'projectId';

    protected $listMessage = 'Project List Fetched Successfully!';

    protected $indexAllMessage = 'All Projects Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Project Details Fetched Successfully!',
        'store' => 'New Project Created Successfully!',
        'update' => 'Project Updated Successfully!',
        'destroy' => 'Project Deleted Successfully!',
        'search' => 'Project Search Results Fetched Successfully!',
        'not_found' => 'Project Not Found',
    ];

    protected function applyListQuery($query, Request $request)
    {
        $query->select('id', 'title', 'category', 'description', 'image', 'link', 'created_at', 'updated_at');
    }
}
