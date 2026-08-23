<?php

namespace App\Http\Controllers\Categories;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class CategoryController extends Controller
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
            $data = DB::table('categories as a')
                ->select('a.id', 'a.name', 'a.description', 'a.parent_id', 'b.name as parent_name')
                ->leftJoin('categories as b', 'a.parent_id', '=', 'b.id')
                ->orderBy('a.id', 'desc')
                ->paginate(10);

            return $this->responseRepository->ResponseSuccess($data, 'Category List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function indexAll(Request $request)
    {
        try {
            $data = DB::table('categories')->paginate($request->perPage ?? 10);
            return $this->responseRepository->ResponseSuccess($data, 'Category List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('perPage', 10);
            $data = DB::table('categories')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('description', 'LIKE', '%' . $search . '%')
                ->paginate($perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Category Search Results Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'parent_id' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $category = new Category();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->parent_id = $request->parent_id;
            $category->save();

            return response()->json(['message' => 'New Category Created Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $data = Category::with('parent')->find($id);

            if (is_null($data)) {
                return $this->responseRepository->ResponseError(null, 'Category Not Found', Response::HTTP_NOT_FOUND);
            }

            $response = [
                'id' => $data->id,
                'name' => $data->name,
                'description' => $data->description,
                'parent_id' => $data->parent_id,
                'parent_name' => $data->parent ? $data->parent->name : null,
            ];

            return $this->responseRepository->ResponseSuccess($response, 'Category Details Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'parent_id' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $category = Category::find($id);

            if (is_null($category)) {
                return response()->json(['message' => 'Category Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            $category->name = $request->name;
            $category->description = $request->description;
            $category->parent_id = $request->parent_id;
            $category->save();

            return response()->json(['message' => 'Category Updated Successfully.', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::find($id);
            if (is_null($category)) {
                return $this->responseRepository->ResponseError(null, 'Category Not Found', Response::HTTP_NOT_FOUND);
            }

            $category->delete();

            return $this->responseRepository->ResponseSuccess(null, 'Category Deleted Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
