<?php

namespace App\Repositories;

use App\Helpers\UploadHelper;
use App\Interfaces\CrudInterface;
use App\Models\Feedback; // Updated model to Feedback
use Illuminate\Support\Str;

class FeedbackRepository implements CrudInterface{

    /**
     * Get All Feedbacks
     *
     * @return collections Array of Feedback Collection
     */
    public function getAll(){
        return Feedback::orderBy('id', 'desc')
        ->paginate(10);
    }

    /**
     * Get Paginated Feedback Data
     *
     * @param int $perPage
     * @return collections Array of Feedback Collection
     */
    public function getPaginatedData($perPage){
        $perPage = isset($perPage) ? $perPage : 12;
        return Feedback::orderBy('id', 'desc')
        ->paginate($perPage);
    }

    /**
     * Get Searchable Feedback Data with Pagination
     *
     * @param string $keyword
     * @param int $perPage
     * @return collections Array of Feedback Collection
     */
    public function searchFeedback($keyword, $perPage){
        $perPage = isset($perPage) ? $perPage : 10;
        return Feedback::where('title', 'like', '%'.$keyword.'%')
        ->orWhere('product', 'like', '%'.$keyword.'%')
        ->orWhere('reference', 'like', '%'.$keyword.'%')
        ->orderBy('id', 'desc')
        ->paginate($perPage);
    }

    /**
     * Create New Feedback
     *
     * @param array $data
     * @return object Feedback Object
     */
    public function create(array $data){
        $feedback = Feedback::create($data); // Change Role to Feedback
        return $feedback;
    }

    /**
     * Delete Feedback
     *
     * @param int $id
     * @return boolean true if deleted otherwise false
     */
    public function delete($id){
        $feedback = Feedback::find($id); // Change Role to Feedback
        if (is_null($feedback))
            return false;

        if (!empty($feedback->icon)) { // Assuming icon is still applicable
            UploadHelper::deleteFile('images/feedbacks/'.$feedback->icon); // Update the path if necessary
        }
        
        $feedback->delete();
        return true;
    }

    /**
     * Get Feedback Detail By ID
     *
     * @param int $id
     * @return object Feedback Object
     */
    public function getByID($id){
        return Feedback::find($id); // Adjust if there's related data
    }

    /**
     * Update Feedback By ID
     *
     * @param int $id
     * @param array $data
     * @return object Updated Feedback Object
     */
    public function update($id, array $data){
        $feedback = Feedback::find($id); // Change Role to Feedback
        
        if(!empty($data['icon'])){ // Assuming icon is still applicable
            $data['icon'] = UploadHelper::update('image', $data['icon'], Str::slug($data['title']).'-'.time(), 'images/feedbacks', $feedback->icon); // Update path
        } else {
            $data['icon'] = $feedback->icon; // Retain old icon if none provided
        }

        if (is_null($feedback))
            return null;

        $feedback->update($data); // Change Role to Feedback
        return $this->getByID($feedback->id);
    }
}
