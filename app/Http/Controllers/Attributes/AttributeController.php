<?php

namespace App\Http\Controllers\Attributes;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attribute; // Ensure you have an Attribute model
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class AttributeController extends Controller
{
    public $responseRepository;

    public function __construct(ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->responseRepository = $rp;
    }

    /**
     * @OA\GET(
     *     path="/api/attributes",
     *     tags={"Attributes"},
     *     summary="Get Attribute List",
     *     description="Get Attribute List as Array",
     *     operationId="index",
     *     @OA\Response(response=200, description="Get Attribute List as Array"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function index()
    {
        try {
            // Using query builder to fetch all attributes
            $data = DB::table('attributes')->orderBy('id', 'desc')->paginate(10); // Adjust pagination as needed

            return $this->responseRepository->ResponseSuccess($data, 'Attribute List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/attributes/view/all",
     *     tags={"Attributes"},
     *     summary="All attributes - Publicly Accessible",
     *     description="All attributes - Publicly Accessible",
     *     operationId="indexAll",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="All attributes - Publicly Accessible"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('perPage', 10);
            $data = DB::table('attributes')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->paginate($perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Attribute Search Results Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function indexAll(Request $request)
    {
        try {
            // Adjust this to fit how you want to paginate attributes
            $data = DB::table('attributes')->paginate($request->perPage ?? 10);
            return $this->responseRepository->ResponseSuccess($data, 'Attribute List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/attributes",
     *     tags={"Attributes"},
     *     summary="Create New Attribute",
     *     description="Create New Attribute",
     *     operationId="store",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Color"),
     *          ),
     *      ),
     *     @OA\Response(response=200, description="Create New Attribute"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            // Check for validation failures
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            // Create a new attribute using the validated data
            $attribute = new Attribute(); // Assuming you have an Attribute model
            $attribute->name = $request->name;
            $attribute->save();

            return response()->json(['message' => 'New Attribute Created Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/attributes/{id}",
     *     tags={"Attributes"},
     *     summary="Show Attribute Details",
     *     description="Show Attribute Details",
     *     operationId="show",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Show Attribute Details"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function show($id)
    {
        try {
            $data = DB::table('attributes')->find($id); // Using query builder to find the attribute by ID
            if (is_null($data)) {
                return $this->responseRepository->ResponseError(null, 'Attribute Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseRepository->ResponseSuccess($data, 'Attribute Details Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/attributes/{id}",
     *     tags={"Attributes"},
     *     summary="Update Attribute",
     *     description="Update Attribute",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Updated Color"),
     *          ),
     *      ),
     *     operationId="update",
     *     @OA\Response(response=200, description="Update Attribute"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the input data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            // Find the attribute by ID
            $attribute = Attribute::find($id);

            if (is_null($attribute)) {
                return response()->json(['message' => 'Attribute Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Update the attribute with new data
            $attribute->name = $request->name;
            $attribute->save();

            return response()->json(['message' => 'Attribute Updated Successfully.', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/attributes/{id}",
     *     tags={"Attributes"},
     *     summary="Delete Attribute",
     *     description="Delete Attribute",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Delete Attribute"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function destroy($id)
    {
        try {
            $attribute = Attribute::find($id);
            if (is_null($attribute)) {
                return $this->responseRepository->ResponseError(null, 'Attribute Not Found', Response::HTTP_NOT_FOUND);
            }

            $attribute->delete();

            return $this->responseRepository->ResponseSuccess(null, 'Attribute Deleted Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
