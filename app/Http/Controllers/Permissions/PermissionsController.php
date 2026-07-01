<?php

namespace App\Http\Controllers\Permissions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Repositories\PermissionRepository;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class PermissionsController extends Controller
{
    public $permissionRepository;
    public $responseRepository;

    public function __construct(PermissionRepository $permissionRepository, ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->permissionRepository = $permissionRepository;
        $this->responseRepository = $rp;
    }

    /**
     * @OA\GET(
     *     path="/api/permissions",
     *     tags={"Permissions"},
     *     summary="Get Permission List",
     *     description="Get Permission List as Array",
     *     operationId="index",
     *     @OA\Response(response=200,description="Get Permission List as Array"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function index()
    {
        try {
            // Using query builder to fetch all permissions
            $data = DB::table('permissions')->orderBy('id', 'desc')->paginate(10); // Adjust pagination as needed
            
            return $this->responseRepository->ResponseSuccess($data, 'Permission List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @OA\GET(
     *     path="/api/permissions/view/all",
     *     tags={"Permissions"},
     *     summary="All Permissions - Publicly Accessible",
     *     description="All Permissions - Publicly Accessible",
     *     operationId="indexAll",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="All Permissions - Publicly Accessible" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function indexAll(Request $request)
    {
        try {
            $data = $this->permissionRepository->getPaginatedData($request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Permission List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/permissions/view/search",
     *     tags={"Permissions"},
     *     summary="Search Permissions - Publicly Accessible",
     *     description="Search Permissions - Publicly Accessible",
     *     operationId="search",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", description="search, eg; Read", example="Read", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Search Permissions - Publicly Accessible" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function search(Request $request)
    {
        try {
            $data = $this->permissionRepository->searchPermission($request->search, $request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Permission List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/permissions",
     *     tags={"Permissions"},
     *     summary="Create New Permission",
     *     description="Create New Permission",
     *     operationId="store",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Read"),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Create New Permission" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function store(Request $request)
{
    try {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required', // Adjusted to check uniqueness in permissions table
            'module' => 'required', // Ensure module is required and a string
            'group' => 'required', // Ensure group is required and a string
        ]);

        // Check for validation failures
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']); 
        }

        // Create a new permission using the validated data
        $permission = new Permission(); // Assuming you have a Permission model
        $permission->name = $request->name;
        $permission->module = $request->module; // Capture module
        $permission->group = $request->group; // Capture group
        $permission->save();

        return response()->json(['message' => 'New Permission Created Successfully!', 'type' => 'success']);
    } catch (\Exception $exception) {
        return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    /**
     * @OA\GET(
     *     path="/api/permissions/{id}",
     *     tags={"Permissions"},
     *     summary="Show Permission Details",
     *     description="Show Permission Details",
     *     operationId="show",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Show Permission Details" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function show($id)
    {
        try {
            $data = $this->permissionRepository->getByID($id);
            if (is_null($data)) {
                return $this->responseRepository->ResponseError(null, 'Permission Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseRepository->ResponseSuccess($data, 'Permission Details Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/permissions/{id}",
     *     tags={"Permissions"},
     *     summary="Update Permission",
     *     description="Update Permission",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Read"),
     *          ),
     *      ),
     *     operationId="update",
     *     @OA\Response( response=200, description="Update Permission" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the input data
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'group' => 'required',
                'module' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            // Find the role by ID
            $role = Permission::find($id);

            if (is_null($role)) {
                return response()->json(['message' => 'Permission Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Update the role with new data
            $role->name = $request->name;
            $role->group = $request->group;
            $role->module = $request->module;
            $role->save();

            return response()->json(['message' => 'Permission Updated Successfully.', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/permissions/{id}",
     *     tags={"Permissions"},
     *     summary="Delete Permission",
     *     description="Delete Permission",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Delete Permission" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function destroy($id)
    {
        try {
            $permissionData = $this->permissionRepository->getByID($id);
            $deleted = $this->permissionRepository->delete($id);
            if (!$deleted) {
                return $this->responseRepository->ResponseError(null, 'Permission Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseRepository->ResponseSuccess($permissionData, 'Permission Deleted Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}