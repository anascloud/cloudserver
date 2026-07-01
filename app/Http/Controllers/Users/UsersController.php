<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class UsersController extends Controller
{
    public $userRepository;
    public $responseRepository;

    public function __construct(UserRepository $userRepository, ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->userRepository = $userRepository;
        $this->responseRepository = $rp;
    }

    /**
     * @OA\GET(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Get User List",
     *     description="Get User List as Array",
     *     operationId="index",
     *     @OA\Response(response=200, description="Get User List as Array"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function index(Request $request)
    {
        try {
            // Get the current page and the number of items per page from the request, defaulting to 10 items per page
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);
    
            // Get the status filter from the request
            $status = $request->input('status', null); // Null if no status is provided

            // Start the query for fetching users
            $query = DB::table('users')->orderBy('id', 'DESC');

            // If a status filter is provided, apply it to the query
            if ($status) {
                $query->where('status', $status);
            }

            // Fetch users with pagination
            $users = $query->paginate($perPage, ['*'], 'page', $currentPage);

    
            // Fetch all roles once to minimize database calls
            $roles = DB::table('roles')->pluck('name', 'id')->toArray() ?? [];
            
            // Initialize an array to hold formatted user data
            $formattedData = [];
    
            // Use foreach to iterate over each user
            foreach ($users as $user) {
                // Decode the roles string to an array of IDs
                $roleIds = json_decode($user->roles, true);
    
                // Initialize an array to hold role names
                $roleNames = [];
    
                // Ensure roleIds is an array
                if (is_array($roleIds)) {
                    // Use foreach to map role IDs to their corresponding names
                    foreach ($roleIds as $id) {
                        // Ensure that the ID is of a valid type (integer or string)
                        if (is_string($id) || is_int($id)) {
                            if (isset($roles[$id]) && !empty($roles[$id])) {
                                $roleNames[] = $roles[$id]; // Add the role name if found
                            }
                        }
                    }
                }
                $formattedData[] = [
                    'id' => $user->id,
                    'fullName' => $user->fullName,
                    'email' => $user->email,
                    'mobileNo' => $user->mobileNo,
                    'country' => $user->country,
                    'status' => $user->status,
                    'avatar' => url($user->avatar),
                    'address' => $user->address,
                    'roles' => !empty($roleNames) ? implode(', ', $roleNames) : [], // Convert to comma-separated string
                    'created_at' => $user->created_at, // Include created_at if needed
                    'updated_at' => $user->updated_at, // Include updated_at if needed
                ];
            }
    
            // Prepare pagination links
            $paginationLinks = [];
            $baseUrl = $request->url();
            
            for ($i = 1; $i <= $users->lastPage(); $i++) {
                $paginationLinks[] = [
                    'url' => $i == $currentPage ? null : $baseUrl . '?page=' . $i,
                    'label' => $i,
                    'active' => $i == $currentPage,
                ];
            }
    
            // Return the formatted response
            return response()->json([
                'status' => true,
                'message' => 'User List Fetched Successfully!',
                'errors' => null,
                'data' => [
                    'current_page' => $users->currentPage(),
                    'data' => $formattedData,
                    'first_page_url' => $users->url(1),
                    'from' => $users->firstItem(),
                    'last_page' => $users->lastPage(),
                    'last_page_url' => $users->url($users->lastPage()),
                    'links' => $paginationLinks,
                    'next_page_url' => $users->nextPageUrl(),
                    'path' => $baseUrl,
                    'per_page' => $users->perPage(),
                    'prev_page_url' => $users->previousPageUrl(),
                    'to' => $users->lastItem(),
                    'total' => $users->total(),
                ]
            ]);
        } catch (\Exception $e) {
            // Return a JSON response with the error message and line number
            return response()->json([$e->getMessage(), $e->getLine()]);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/users/view/all",
     *     tags={"Users"},
     *     summary="All Users - Publicly Accessible",
     *     description="All Users - Publicly Accessible",
     *     operationId="indexAll",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="All Users - Publicly Accessible"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function indexAll(Request $request)
    {
        try {
            $data = $this->userRepository->getPaginatedData($request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'User List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Create New User",
     *     description="Create New User",
     *     operationId="store",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *              @OA\Property(property="password", type="string", example="password123"),
     *              @OA\Property(property="roles", type="array", @OA\Items(type="integer"), example={1, 2}),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Create New User"),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
        public function store(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'fullName' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'mobileNo' => 'required|string|max:20',
                    'avatar' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
                ]);

                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']); 
                }

                $user = new User();
                $user->fullName = $request->fullName;
                $user->email = $request->email;
                $user->mobileNo = $request->mobileNo;
                $user->country = $request->country;
                $user->address = $request->address;
                $user->password = bcrypt($request->password);
                $user->roles = $request->roles; // Store as JSON
                $user->status = "Active";

                if ($request->hasFile('avatar')) {
                    $image = $request->file('avatar');
                    $filename = time() . '.' . $image->getClientOriginalExtension();
                    $avatarPath = $image->move(public_path('uploads'), $filename);  // Save to public/uploads
                    $user->avatar = 'uploads/' . $filename; // Store relative path in DB
                }
                

                $user->save();

                return response()->json(['message' => 'New User Created Successfully.', 'type' => 'success']);
            } catch (\Exception $exception) {
                return response()->json(['message' => $exception->getMessage(), 'type' => 'error']); 
            }
        }



    /**
     * @OA\GET(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Show User Details",
     *     description="Show User Details",
     *     operationId="show",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Show User Details"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function show($id)
    {   
        try {
            // Retrieve the user by ID
            $data = $this->userRepository->getByID($id); // Directly use the User model

            // Check if the user is found
            if (is_null($data)) {
                return $this->responseRepository->ResponseError(null, 'User Not Found', Response::HTTP_NOT_FOUND);
            }

            // Set the avatar URL
            $data->avatar = url($data->avatar);
            // Return success response with user data
            return $this->responseRepository->ResponseSuccess($data, 'User Details Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Update User",
     *     description="Update User",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *              @OA\Property(property="roles", type="array", @OA\Items(type="integer"), example={1, 2}),
     *          ),
     *      ),
     *     operationId="update",
     *     @OA\Response(response=200, description="Update User"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function update(Request $request, $id)
    {   
        if(!empty($request->status) && !empty($id)){
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found', 'type' => 'error']); 
            }
            $user->status = $request->status;
            $saved = $user->save();

            if($saved){
                $user->avatarpath = url($user->avatar);
                return response()->json(['data'=> $user,'message' => 'User updated successfully', 'type' => 'success']);
            }else{
                return response()->json(['message' => 'User updated Failed', 'type' => 'error']);
            }
        }else{
            $validator = Validator::make($request->all(), [
                'fullName' => 'required|string|max:255',
                // 'email' => 'required|email|max:255|unique:users,email,' . $id, // This allows the current email to remain unchanged
                'mobileNo' => 'required|string|max:20',
                'avatar' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']); 
            }

            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'User not found', 'type' => 'error']); 
            }

            // Update user info
            $user->fullName = $request->fullName;
            // $user->email = $request->email;
            $user->mobileNo = $request->mobileNo;
            $user->country = $request->country;
            $user->address = $request['address'];
            $user->roles = $request->roles;

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $avatarPath = $image->move(public_path('uploads'), $filename);  // Save to public/uploads
                $user->avatar = 'uploads/' . $filename; // Store relative path in DB
            }        

            // Save the updated user information
            $saved = $user->save();

            if($saved){
                $user->avatarpath = url($user->avatar);
                return response()->json(['data'=> $user,'message' => 'Profile updated successfully', 'type' => 'success']);
            }else{
                return response()->json(['message' => 'Profile updated Failed', 'type' => 'error']);
            }
        }
        
    }

    /**
     * @OA\DELETE(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Delete User",
     *     description="Delete User",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Delete User"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function destroy($id)
    {
        try {
            $userData = $this->userRepository->getByID($id);
            $deleted = $this->userRepository->delete($id);
            if (!$deleted) {
                return $this->responseRepository->ResponseError(null, 'User Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseRepository->ResponseSuccess($userData, 'User Deleted Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
