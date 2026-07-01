<?php

namespace App\Http\Controllers\Stocks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Repositories\StockRepository;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class StocksController extends Controller
{
    public $stockRepository;
    public $responseRepository;

    public function __construct(ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->responseRepository = $rp;
    }

    /**
     * @OA\GET(
     *     path="/api/stocks",
     *     tags={"Stocks"},
     *     summary="Get Stock List",
     *     description="Get Stock List as Array",
     *     operationId="index",
     *     @OA\Response(response=200,description="Get Stock List as Array"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function index()
    {
        try {
            // Using query builder to fetch all stocks
            $data = DB::table('stocks')
            ->join('products', 'stocks.product', '=', 'products.id') // Assuming stocks table has product_id
            ->select('stocks.*', 'products.name as product_name') // Select required fields
            ->orderBy('stocks.id', 'desc')
            ->paginate(10);
            
            return $this->responseRepository->ResponseSuccess($data, 'Stock List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/stocks/view/all",
     *     tags={"Stocks"},
     *     summary="All Stocks - Publicly Accessible",
     *     description="All Stocks - Publicly Accessible",
     *     operationId="indexAll",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="All Stocks - Publicly Accessible" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function indexAll(Request $request)
    {
        try {
            $data = $this->stockRepository->getPaginatedData($request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Stock List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/stocks/view/search",
     *     tags={"Stocks"},
     *     summary="Search Stocks - Publicly Accessible",
     *     description="Search Stocks - Publicly Accessible",
     *     operationId="search",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", description="search, eg; ProductName", example="Product A", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Search Stocks - Publicly Accessible" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function search(Request $request)
    {
        try {
            $data = $this->stockRepository->searchStock($request->search, $request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Stock List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/stocks",
     *     tags={"Stocks"},
     *     summary="Create New Stock",
     *     description="Create New Stock",
     *     operationId="store",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="product", type="string", example="Product A"),
     *              @OA\Property(property="warehouse", type="string", example="Warehouse 1"),
     *              @OA\Property(property="quantity", type="integer", example=100),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Create New Stock" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'product' => 'required|string',
                'warehouse' => 'required|string',
                'quantity' => 'required|integer',
            ]);

            // Check for validation failures
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']); 
            }

            // Create a new stock record using the validated data
            $stock = new Stock(); // Assuming you have a Stock model
            $stock->product = $request->product;
            $stock->warehouse = $request->warehouse;
            $stock->quantity = $request->quantity;
            $stock->save();

            return response()->json(['message' => 'New Stock Created Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/stocks/{id}",
     *     tags={"Stocks"},
     *     summary="Show Stock Details",
     *     description="Show Stock Details",
     *     operationId="show",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Show Stock Details" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
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

    /**
     * @OA\PUT(
     *     path="/api/stocks/{id}",
     *     tags={"Stocks"},
     *     summary="Update Stock",
     *     description="Update Stock",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="product", type="string", example="Product A"),
     *              @OA\Property(property="warehouse", type="string", example="Warehouse 1"),
     *              @OA\Property(property="quantity", type="integer", example=100),
     *          ),
     *      ),
     *     operationId="update",
     *     @OA\Response( response=200, description="Update Stock" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the input data
            $validator = Validator::make($request->all(), [
                'product' => 'required|string',
                'warehouse' => 'required|string',
                'quantity' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            // Find the stock by ID
            $stock = Stock::find($id);

            if (is_null($stock)) {
                return response()->json(['message' => 'Stock Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Update the stock with new data
            $stock->product = $request->product;
            $stock->warehouse = $request->warehouse;
            $stock->quantity = $request->quantity;
            $stock->save();

            return response()->json(['message' => 'Stock Updated Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/stocks/{id}",
     *     tags={"Stocks"},
     *     summary="Delete Stock",
     *     description="Delete Stock",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Delete Stock" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function destroy($id)
    {
        try {
            // Find the stock by ID
            $stock = Stock::find($id);

            if (is_null($stock)) {
                return response()->json(['message' => 'Stock Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Delete the stock record
            $stock->delete();

            return response()->json(['message' => 'Stock Deleted Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
