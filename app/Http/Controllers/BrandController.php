<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand; // Ensure you have a Brand model
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class BrandController extends Controller
{
    public $responseRepository;

    public function __construct(ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->responseRepository = $rp;
    }

    public function index()
    {
        try {
            // Using query builder to fetch all brands
            $data = DB::table('brands')->orderBy('id', 'desc')->paginate(10); // Adjust pagination as needed
            
            return $this->responseRepository->ResponseSuccess($data, 'Brand List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function indexAll(Request $request)
    {
        try {
            // Adjust this to fit how you want to paginate brands
            $data = DB::table('brands')->paginate($request->perPage ?? 10);
            return $this->responseRepository->ResponseSuccess($data, 'Brand List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

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

            // Create a new brand using the validated data
            $brand = new Brand(); // Assuming you have a Brand model
            $brand->name = $request->name;
            $brand->save();

            return response()->json(['message' => 'New Brand Created Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $data = DB::table('brands')->find($id); // Using query builder to find the brand by ID
            if (is_null($data)) {
                return $this->responseRepository->ResponseError(null, 'Brand Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseRepository->ResponseSuccess($data, 'Brand Details Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

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

            // Find the brand by ID
            $brand = Brand::find($id);

            if (is_null($brand)) {
                return response()->json(['message' => 'Brand Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Update the brand with new data
            $brand->name = $request->name;
            $brand->save();

            return response()->json(['message' => 'Brand Updated Successfully.', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $brand = Brand::find($id);
            if (is_null($brand)) {
                return $this->responseRepository->ResponseError(null, 'Brand Not Found', Response::HTTP_NOT_FOUND);
            }

            $brand->delete();

            return $this->responseRepository->ResponseSuccess(null, 'Brand Deleted Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
