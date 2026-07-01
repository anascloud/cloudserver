<?php

namespace App\Http\Controllers\Categories;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category; // Ensure you have a Category model
use App\Repositories\CategoryRepository; // You may need to create this repository
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class CategoryController extends Controller
{
    public $categoryRepository;
    public $responseRepository;

    public function __construct(ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        // $this->categoryRepository = $categoryRepository;
        $this->responseRepository = $rp;
    }

    /**
     * @OA\GET(
     *     path="/api/categories",
     *     tags={"Categories"},
     *     summary="Get Category List",
     *     description="Get Category List as Array",
     *     operationId="index",
     *     @OA\Response(response=200,description="Get Category List as Array"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function index()
{
    try {
        // Using query builder to fetch all categories with their parent category names
        $data = DB::table('categories as a')
                    ->select('a.id', 'a.name', 'a.parent_id', 'b.name as parent_name')
                    ->leftJoin('categories as b', 'a.parent_id', '=', 'b.id') // Corrected the join condition
                    ->orderBy('a.id', 'desc')
                    ->paginate(10); // Adjust pagination as needed

        return $data;
        // return $this->responseRepository->ResponseSuccess($data, 'Category List Fetched Successfully!');
    } catch (\Exception $e) {
        return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    /**
     * @OA\GET(
     *     path="/api/categories/view/all",
     *     tags={"Categories"},
     *     summary="All Categories - Publicly Accessible",
     *     description="All Categories - Publicly Accessible",
     *     operationId="indexAll",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="All Categories - Publicly Accessible" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function indexAll(Request $request)
    {
        try {
            $data = $this->categoryRepository->getPaginatedData($request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Category List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/categories",
     *     tags={"Categories"},
     *     summary="Create New Category",
     *     description="Create New Category",
     *     operationId="store",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Electronics"),
     *              @OA\Property(property="description", type="string", example="Category for electronic items."),
     *              @OA\Property(property="parent_id", type="integer", example=1),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Create New Category" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'parent_id' => 'nullable', // Ensure parent_id exists in categories table if provided
            ]);

            // Check for validation failures
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            // Create a new category using the validated data
            $category = new Category(); // Assuming you have a Category model
            $category->name = $request->name;
            $category->description = $request->description; // Capture description
            $category->parent_id = $request->parent_id; // Capture parent_id
            $category->save();

            return response()->json(['message' => 'New Category Created Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     summary="Show Category Details",
     *     description="Show Category Details",
     *     operationId="show",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Show Category Details" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function show($id)
{
    try {
        // Eager load the parent category (if exists)
        $data = Category::with('parent')->find($id);

        if (is_null($data)) {
            return $this->responseRepository->ResponseError(null, 'Category Not Found', Response::HTTP_NOT_FOUND);
        }

        // Format the response to include parent category name
        $response = [
            'id' => $data->id,
            'name' => $data->name,
            'category' => $data->parent ? $data->parent->name : null // Check if parent exists
        ];

        return $this->responseRepository->ResponseSuccess($response, 'Category Details Fetched Successfully!');
    } catch (\Exception $e) {
        return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    /**
     * @OA\PUT(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     summary="Update Category",
     *     description="Update Category",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Electronics"),
     *              @OA\Property(property="description", type="string", example="Updated description."),
     *              @OA\Property(property="parent_id", type="integer", example=1),
     *          ),
     *      ),
     *     operationId="update",
     *     @OA\Response(response=200, description="Update Category" ),
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
                'parent_id' => 'nullable', // Validate parent_id if provided
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            // Find the category by ID
            $category = Category::find($id);

            if (is_null($category)) {
                return response()->json(['message' => 'Category Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Update the category with new data
            $category->name = $request->name;
            $category->description = $request->description;
            $category->parent_id = $request->parent_id;
            $category->save();

            return response()->json(['message' => 'Category Updated Successfully.', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     summary="Delete Category",
     *     description="Delete Category",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Delete Category" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function destroy($id)
    {
        try {
            $categoryData = $this->categoryRepository->getByID($id);
            $deleted = $this->categoryRepository->delete($id);
            if (!$deleted) {
                return $this->responseRepository->ResponseError(null, 'Category Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseRepository->ResponseSuccess($categoryData, 'Category Deleted Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
