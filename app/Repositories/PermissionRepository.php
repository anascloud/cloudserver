<?php

namespace App\Repositories;

use App\Helpers\UploadHelper;
use App\Interfaces\CrudInterface;
use App\Models\Permission; // Updated model to Permission
use Illuminate\Support\Str;

class PermissionRepository implements CrudInterface{

    /**
     * Get All Permissions
     *
     * @return collections Array of Permission Collection
     */
    public function getAll(){
        return Permission::orderBy('id', 'desc')
        ->paginate(10);
    }

    /**
     * Get Paginated Permission Data
     *
     * @param int $perPage
     * @return collections Array of Permission Collection
     */
    public function getPaginatedData($perPage){
        $perPage = isset($perPage) ? $perPage : 12;
        return Permission::orderBy('id', 'desc')
        ->paginate($perPage);
    }

    /**
     * Get Searchable Permission Data with Pagination
     *
     * @param string $keyword
     * @param int $perPage
     * @return collections Array of Permission Collection
     */
    public function searchPermission($keyword, $perPage){
        $perPage = isset($perPage) ? $perPage : 10;
        return Permission::where('name', 'like', '%'.$keyword.'%')
        ->orWhere('module', 'like', '%'.$keyword.'%') // Changed from permission to module
        ->orderBy('id', 'desc')
        ->paginate($perPage);
    }

    /**
     * Create New Permission
     *
     * @param array $data
     * @return object Permission Object
     */
    public function create(array $data){
        return response()->json($data); // Remove this line in production
        $permission = Permission::create($data); // Change Role to Permission
        return $permission;
    }

    /**
     * Delete Permission
     *
     * @param int $id
     * @return boolean true if deleted otherwise false
     */
    public function delete($id){
        $permission = Permission::find($id); // Change Role to Permission
        if (is_null($permission))
            return false;

        if (!empty($permission->icon)) { // Assuming icon is still applicable
            UploadHelper::deleteFile('images/permissions/'.$permission->icon); // Update the path if necessary
        }
        
        $permission->delete();
        return true;
    }

    /**
     * Get Permission Detail By ID
     *
     * @param int $id
     * @return object Permission Object
     */
    public function getByID($id){
        return Permission::find($id); // Adjust if there's related data
    }

    /**
     * Update Permission By ID
     *
     * @param int $id
     * @param array $data
     * @return object Updated Permission Object
     */
    public function update($id, array $data){
        $permission = Permission::find($id); // Change Role to Permission
        
        if(!empty($data['icon'])){ // Assuming icon is still applicable
            $data['icon'] = UploadHelper::update('image', $data['icon'], Str::slug($data['name']).'-'.time(), 'images/permissions', $permission->icon); // Update path
        } else {
            $data['icon'] = $permission->icon; // Retain old icon if none provided
        }

        if (is_null($permission))
            return null;

        $permission->update($data); // Change Role to Permission
        return $this->getByID($permission->id);
    }
}
