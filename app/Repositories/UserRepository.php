<?php

namespace App\Repositories;

use Illuminate\Support\Str;
use App\Helpers\UploadHelper;
use App\Interfaces\CrudInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository implements CrudInterface{

    /**
     * Get All Users
     *
     * @return collections Array of User Collection
     */
    public function getAll(){
        return User::orderBy('id', 'desc')
        ->paginate(20);
    }

    /**
     * Get Paginated User Data
     *
     * @param int $perPage
     * @return collections Array of User Collection
     */
    public function getPaginatedData($perPage){
        $perPage = isset($perPage) ? $perPage : 12;
        return User::orderBy('id', 'desc')
        ->paginate($perPage);
    }

    /**
     * Get Searchable User Data with Pagination
     *
     * @param string $keyword
     * @param int $perPage
     * @return collections Array of User Collection
     */
    public function searchUser($keyword, $perPage){
        $perPage = isset($perPage) ? $perPage : 10;
        return User::where('name', 'like', '%'.$keyword.'%')
        ->orWhere('email', 'like', '%'.$keyword.'%')
        ->orderBy('id', 'desc')
        ->paginate($perPage);
    }

    /**
     * Create New User
     *
     * @param array $data
     * @return object User Object
     */
    public function create(array $data){
        if (!empty($data['profile_image'])) {
            $data['profile_image'] = UploadHelper::upload('image', $data['profile_image'], Str::slug($data['name']).'-'.time(), 'images/users');  
        }
        $user = User::create($data);
        return $user;
    }

    /**
     * Delete User
     *
     * @param int $id
     * @return boolean true if deleted otherwise false
     */
    public function delete($id){
        $user = User::find($id);
        if (is_null($user))
            return false;

        if (!empty($user->profile_image)) {
            UploadHelper::deleteFile('images/users/'.$user->profile_image);
        }
        
        $user->delete();
        return true;
    }

    /**
     * Get User Detail By ID
     *
     * @param int $id
     * @return object User Object
     */
    public function getByID($id){
        return User::all()  // Adjust if there's related data
        ->find($id);
    }

    /**
     * Update User By ID
     *
     * @param int $id
     * @param array $data
     * @return object Updated User Object
     */
    public function update($id, array $data){
        $user = User::find($id);
        
        if(!empty($data['profile_image'])){
            $data['profile_image'] = UploadHelper::update('image', $data['profile_image'], Str::slug($data['name']).'-'.time(), 'images/users', $user->profile_image);           
        } else {
            $data['profile_image'] = $user->profile_image;
        }

        if (is_null($user))
            return null;

        $user->update($data);
        return $this->getByID($user->id);
    }
}