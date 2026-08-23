<?php

namespace App\Http\Controllers\Stocks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class StocksController extends Controller
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
            $data = DB::table('stocks')
                ->join('products', 'stocks.product', '=', 'products.id')
                ->select('stocks.*', 'products.name as product_name')
                ->orderBy('stocks.id', 'desc')
                ->paginate(10);

            return $this->responseRepository->ResponseSuccess($data, 'Stock List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function indexAll(Request $request)
    {
        try {
            $data = DB::table('stocks')->paginate($request->perPage ?? 10);
            return $this->responseRepository->ResponseSuccess($data, 'Stock List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('perPage', 10);
            $data = DB::table('stocks')
                ->join('products', 'stocks.product', '=', 'products.id')
                ->select('stocks.*', 'products.name as product_name')
                ->where('products.name', 'LIKE', '%' . $search . '%')
                ->orWhere('stocks.warehouse', 'LIKE', '%' . $search . '%')
                ->paginate($perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Stock Search Results Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product' => 'required|string',
                'warehouse' => 'required|string',
                'quantity' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $stock = new Stock();
            $stock->product = $request->product;
            $stock->warehouse = $request->warehouse;
            $stock->quantity = $request->quantity;
            $stock->save();

            return response()->json(['message' => 'New Stock Created Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $data = DB::table('stocks')->find($id);
            if (is_null($data)) {
                return $this->responseRepository->ResponseError(null, 'Stock Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseRepository->ResponseSuccess($data, 'Stock Details Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product' => 'required|string',
                'warehouse' => 'required|string',
                'quantity' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $stock = Stock::find($id);

            if (is_null($stock)) {
                return response()->json(['message' => 'Stock Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            $stock->product = $request->product;
            $stock->warehouse = $request->warehouse;
            $stock->quantity = $request->quantity;
            $stock->save();

            return response()->json(['message' => 'Stock Updated Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $stock = Stock::find($id);

            if (is_null($stock)) {
                return response()->json(['message' => 'Stock Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            $stock->delete();

            return response()->json(['message' => 'Stock Deleted Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
