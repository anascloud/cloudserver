<?php

namespace App\Http\Controllers\Roles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use App\Repositories\RoleRepository;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use DB;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    public $roleRepository;
    public $responseRepository;

    public function __construct(RoleRepository $roleRepository, ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->roleRepository = $roleRepository;
        $this->responseRepository = $rp;
    }

    /**
     * @OA\GET(
     *     path="/api/roles",
     *     tags={"Roles"},
     *     summary="Get Role List",
     *     description="Get Role List as Array",
     *     operationId="index",
     *     @OA\Response(response=200,description="Get Role List as Array"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

     public function index(Request $request)
    {
        try {
            $perPage = $request->input('params.pageSize', 10);
            $currentPage = $request->input('params.pageIndex', 1);

            // Ensure $currentPage starts at 1 (adjust if necessary)
            $currentPage = max(1, $currentPage + 1);

            $roles = DB::table('roles')->orderBy('id', 'DESC')->paginate($perPage, ['*'], 'page', $currentPage);

            $permissions = DB::table('permissions')->pluck('name', 'id')->toArray();

            $formattedData = $roles->getCollection()->map(function ($role) use ($permissions) {
                // Decode the permissions JSON and ensure it's an array or default to an empty array
                $permissionIds = is_array(json_decode($role->permissions, true)) ? json_decode($role->permissions, true) : [];

                $permissionNames = [];
                foreach ($permissionIds as $id) {
                    if (isset($permissions[$id])) {
                        $permissionNames[] = $permissions[$id];
                    }
                }

                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => implode(', ', $permissionNames),
                    'created_at' => $role->created_at,
                    'updated_at' => $role->updated_at,
                ];
            });

            $paginationLinks = [];
            $baseUrl = $request->url();

            for ($i = 1; $i <= $roles->lastPage(); $i++) {
                $paginationLinks[] = [
                    'url' => $i == $currentPage ? null : $baseUrl . '?page=' . $i,
                    'label' => $i,
                    'active' => $i == $currentPage,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Role List Fetched Successfully!',
                'errors' => null,
                'data' => [
                    'current_page' => $roles->currentPage(),
                    'data' => $formattedData,
                    'first_page_url' => $roles->url(1),
                    'from' => $roles->firstItem(),
                    'last_page' => $roles->lastPage(),
                    'last_page_url' => $roles->url($roles->lastPage()),
                    'links' => $paginationLinks,
                    'next_page_url' => $roles->nextPageUrl(),
                    'path' => $baseUrl,
                    'per_page' => $roles->perPage(),
                    'prev_page_url' => $roles->previousPageUrl(),
                    'to' => $roles->lastItem(),
                    'total' => $roles->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage(), $e->getLine()]);
        }
    }     

    
    /**
     * @OA\GET(
     *     path="/api/roles/view/all",
     *     tags={"Roles"},
     *     summary="All Roles - Publicly Accessible",
     *     description="All Roles - Publicly Accessible",
     *     operationId="indexAll",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="All Roles - Publicly Accessible" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function indexAll(Request $request)
    {
        try {
            $data = $this->roleRepository->getPaginatedData($request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Role List Fetched Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * @OA\GET(
     *     path="/api/roles/view/search",
     *     tags={"Roles"},
     *     summary="Search Roles - Publicly Accessible",
     *     description="Search Roles - Publicly Accessible",
     *     operationId="search",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", description="search, eg; Admin", example="Admin", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Search Roles - Publicly Accessible" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function search(Request $request)
    {
        try {
            $data = $this->roleRepository->searchRole($request->search, $request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Role List Fetched Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/roles",
     *     tags={"Roles"},
     *     summary="Create New Role",
     *     description="Create New Role",
     *     operationId="store",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Admin"),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Create New Role" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function store(Request $request)
    {   
        // return response()->json($request->all());
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|unique:roles,name',
                'permissions' => 'nullable|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']); 
            }

            $user = new Role();
            $user->name = $request->name;
            $user->permissions = $request->permissions;
            $user->save();
            return response()->json(['message' => 'New Role Created Successfully.', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error']);
        }
    }


    /**
     * @OA\GET(
     *     path="/api/roles/{id}",
     *     tags={"Roles"},
     *     summary="Show Role Details",
     *     description="Show Role Details",
     *     operationId="show",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Show Role Details" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function show($id)
    {
        try {
            $data = $this->roleRepository->getByID($id);
            if(is_null($data))
                return $this->responseRepository->ResponseError(null, 'Role Not Found', Response::HTTP_NOT_FOUND);

            return $this->responseRepository->ResponseSuccess($data, 'Role Details Fetched Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/roles/{id}",
     *     tags={"Roles"},
     *     summary="Update Role",
     *     description="Update Role",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Admin"),
     *          ),
     *      ),
     *     operationId="update",
     *     @OA\Response( response=200, description="Update Role" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the input data
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|unique:roles,name,' . $id,
                'permissions' => 'nullable|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            // Find the role by ID
            $role = Role::find($id);

            if (is_null($role)) {
                return response()->json(['message' => 'Role Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Update the role with new data
            $role->name = $request->name;
            $role->permissions = $request->permissions;
            $role->save();

            return response()->json(['message' => 'Role Updated Successfully.', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/roles/{id}",
     *     tags={"Roles"},
     *     summary="Delete Role",
     *     description="Delete Role",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Delete Role" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function destroy($id)
    {
        try {
            $roleData =  $this->roleRepository->getByID($id);
            $deleted = $this->roleRepository->delete($id);
            if(!$deleted)
                return $this->responseRepository->ResponseError(null, 'Role Not Found', Response::HTTP_NOT_FOUND);

            return $this->responseRepository->ResponseSuccess($roleData, 'Role Deleted Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}