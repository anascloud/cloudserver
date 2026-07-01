<?php

namespace App\Repositories;

use App\Helpers\UploadHelper;
use App\Interfaces\CrudInterface;
use App\Models\Role;
use Illuminate\Support\Str;

class RoleRepository implements CrudInterface{

    /**
     * Get All Roles
     *
     * @return collections Array of Role Collection
     */
    public function getAll(){
        return Role::orderBy('id', 'desc')
        ->paginate(10);
    }

    /**
     * Get Paginated Role Data
     *
     * @param int $perPage
     * @return collections Array of Role Collection
     */
    public function getPaginatedData($perPage){
        $perPage = isset($perPage) ? $perPage : 12;
        return Role::orderBy('id', 'desc')
        ->paginate($perPage);
    }

    /**
     * Get Searchable Role Data with Pagination
     *
     * @param string $keyword
     * @param int $perPage
     * @return collections Array of Role Collection
     */
    public function searchRole($keyword, $perPage){
        $perPage = isset($perPage) ? $perPage : 10;
        return Role::where('name', 'like', '%'.$keyword.'%')
        ->orWhere('permission', 'like', '%'.$keyword.'%')
        ->orderBy('id', 'desc')
        ->paginate($perPage);
    }

    /**
     * Create New Role
     *
     * @param array $data
     * @return object Role Object
     */
    public function create(array $data){
        return response()->JSON($data);
        $role = Role::create($data);
        return $role;
    }

    /**
     * Delete Role
     *
     * @param int $id
     * @return boolean true if deleted otherwise false
     */
    public function delete($id){
        $role = Role::find($id);
        if (is_null($role))
            return false;

        if (!empty($role->icon)) {
            UploadHelper::deleteFile('images/roles/'.$role->icon);
        }
        
        $role->delete();
        return true;
    }

    /**
     * Get Role Detail By ID
     *
     * @param int $id
     * @return object Role Object
     */
    public function getByID($id){
        return Role::find($id); // Adjust if there's related data
    }

    /**
     * Update Role By ID
     *
     * @param int $id
     * @param array $data
     * @return object Updated Role Object
     */
    public function update($id, array $data){
        $role = Role::find($id);
        
        if(!empty($data['icon'])){
            $data['icon'] = UploadHelper::update('image', $data['icon'], Str::slug($data['name']).'-'.time(), 'images/roles', $role->icon);           
        } else {
            $data['icon'] = $role->icon;
        }

        if (is_null($role))
            return null;

        $role->update($data);
        return $this->getByID($role->id);
    }
}