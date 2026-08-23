<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Brand;
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
            $data = DB::table('brands')->orderBy('id', 'desc')->paginate(10);
            return $this->responseRepository->ResponseSuccess($data, 'Brand List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function indexAll(Request $request)
    {
        try {
            $data = DB::table('brands')->paginate($request->perPage ?? 10);
            return $this->responseRepository->ResponseSuccess($data, 'Brand List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('perPage', 10);
            $data = DB::table('brands')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->paginate($perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Brand Search Results Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $brand = new Brand();
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
            $data = DB::table('brands')->find($id);
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
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $brand = Brand::find($id);

            if (is_null($brand)) {
                return response()->json(['message' => 'Brand Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

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
