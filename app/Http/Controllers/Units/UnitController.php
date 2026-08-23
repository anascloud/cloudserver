<?php

namespace App\Http\Controllers\Units;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Unit; // Ensure you have a Unit model
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class UnitController extends Controller
{
    public $responseRepository;

    public function __construct(ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->responseRepository = $rp;
    }

    /**
     * @OA\GET(
     *     path="/api/units",
     *     tags={"Units"},
     *     summary="Get Unit List",
     *     description="Get Unit List as Array",
     *     operationId="index",
     *     @OA\Response(response=200, description="Get Unit List as Array"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function index()
    {
        try {
            // Using query builder to fetch all units
            $data = DB::table('units')->orderBy('id', 'desc')->paginate(10); // Adjust pagination as needed
            
            return $this->responseRepository->ResponseSuccess($data, 'Unit List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/units/view/all",
     *     tags={"Units"},
     *     summary="All units - Publicly Accessible",
     *     description="All units - Publicly Accessible",
     *     operationId="indexAll",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="All units - Publicly Accessible"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('perPage', 10);
            $data = DB::table('units')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->paginate($perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Unit Search Results Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function indexAll(Request $request)
    {
        try {
            // Adjust this to fit how you want to paginate units
            $data = DB::table('units')->paginate($request->perPage ?? 10);
            return $this->responseRepository->ResponseSuccess($data, 'Unit List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/units",
     *     tags={"Units"},
     *     summary="Create New Unit",
     *     description="Create New Unit",
     *     operationId="store",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Kilogram"),
     *          ),
     *      ),
     *     @OA\Response(response=200, description="Create New Unit"),
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

            // Create a new unit using the validated data
            $unit = new Unit(); // Assuming you have a Unit model
            $unit->name = $request->name;
            $unit->save();

            return response()->json(['message' => 'New Unit Created Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/units/{id}",
     *     tags={"Units"},
     *     summary="Show Unit Details",
     *     description="Show Unit Details",
     *     operationId="show",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Show Unit Details"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function show($id)
    {
        try {
            $data = DB::table('units')->find($id); // Using query builder to find the unit by ID
            if (is_null($data)) {
                return $this->responseRepository->ResponseError(null, 'Unit Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseRepository->ResponseSuccess($data, 'Unit Details Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/units/{id}",
     *     tags={"Units"},
     *     summary="Update Unit",
     *     description="Update Unit",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Updated Kilogram"),
     *          ),
     *      ),
     *     operationId="update",
     *     @OA\Response(response=200, description="Update Unit"),
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

            // Find the unit by ID
            $unit = Unit::find($id);

            if (is_null($unit)) {
                return response()->json(['message' => 'Unit Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Update the unit with new data
            $unit->name = $request->name;
            $unit->save();

            return response()->json(['message' => 'Unit Updated Successfully.', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/units/{id}",
     *     tags={"Units"},
     *     summary="Delete Unit",
     *     description="Delete Unit",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Delete Unit"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function destroy($id)
    {
        try {
            $unit = Unit::find($id);
            if (is_null($unit)) {
                return $this->responseRepository->ResponseError(null, 'Unit Not Found', Response::HTTP_NOT_FOUND);
            }

            $unit->delete();

            return $this->responseRepository->ResponseSuccess(null, 'Unit Deleted Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
