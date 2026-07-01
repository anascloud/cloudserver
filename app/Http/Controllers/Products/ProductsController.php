<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use DB;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public $productRepository;
    public $responseRepository;

    public function __construct(ProductRepository $productRepository, ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->productRepository = $productRepository;
        $this->responseRepository = $rp;
    }

    /**
     * @OA\GET(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Get Product List",
     *     description="Get Product List as Array",
     *     operationId="index",
     *     @OA\Response(response=200,description="Get Product List as Array"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function index(Request $request)
{
    try {
        // Get the current page and the number of items per page from the request, defaulting to 10 items per page
        $perPage = $request->input('per_page', 10);
        $currentPage = $request->input('page', 1);

        // Get the status filter from the request (if applicable)
        // $status = $request->input('status', null); // Uncomment if you have a status filter

        // Start the query for fetching products
        $query = DB::table('products as p')
            ->leftJoin('categories as c', 'p.category', '=', 'c.id') // Join categories table to get category name
            ->leftJoin('units as u', 'p.unit', '=', 'u.id') // Join categories table to get category name
            ->select(
                'p.id',
                'p.name',
                'p.code',
                'p.actualPrice',
                'p.sellPrice',
                'p.description',
                'p.brand',
                'p.unit',
                'p.status',
                'p.thumbnail',
                'p.created_at',
                'p.updated_at',
                'c.name as category_name', // Select category name from categories table
                'u.name as unit_name' // Select category name from categories table
            )
            ->orderBy('p.id', 'DESC');

        // If a status filter is provided, apply it to the query
        // if ($status) {
        //     $query->where('status', $status); // Uncomment if filtering by status
        // }

        // Fetch products with pagination
        $products = $query->paginate($perPage, ['*'], 'page', $currentPage);

        // Initialize an array to hold formatted product data
        $formattedData = [];

        // Use foreach to iterate over each product
        foreach ($products as $product) {
            $formattedData[] = [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'actualPrice' => $product->actualPrice,
                'sellPrice' => $product->sellPrice,
                'description' => $product->description,
                'category' => $product->category_name,
                'brand' => $product->brand,
                'unit' => $product->unit_name,
                'status' => $product->status,
                'thumbnail' => url($product->thumbnail), // Assuming thumbnails are stored with a path
                'created_at' => $product->created_at, // Include created_at if needed
                'updated_at' => $product->updated_at, // Include updated_at if needed
            ];
        }

        // Prepare pagination links
        $paginationLinks = [];
        $baseUrl = $request->url();

        for ($i = 1; $i <= $products->lastPage(); $i++) {
            $paginationLinks[] = [
                'url' => $i == $currentPage ? null : $baseUrl . '?page=' . $i . '&per_page=' . $perPage,
                'label' => $i,
                'active' => $i == $currentPage,
            ];
        }

        // Return the formatted response
        return response()->json([
            'status' => true,
            'message' => 'Product List Fetched Successfully!',
            'errors' => null,
            'data' => [
                'current_page' => $products->currentPage(),
                'data' => $formattedData,
                'first_page_url' => $products->url(1),
                'from' => $products->firstItem(),
                'last_page' => $products->lastPage(),
                'last_page_url' => $products->url($products->lastPage()),
                'links' => $paginationLinks,
                'next_page_url' => $products->nextPageUrl(),
                'path' => $baseUrl,
                'per_page' => $products->perPage(),
                'prev_page_url' => $products->previousPageUrl(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
            ]
        ]);
    } catch (\Exception $e) {
        // Return a JSON response with the error message and line number
        return response()->json([$e->getMessage(), $e->getLine()]);
    }
}

    
    /**
     * @OA\GET(
     *     path="/api/products/view/all",
     *     tags={"Products"},
     *     summary="All Products - Publicly Accessable",
     *     description="All Products - Publicly Accessable",
     *     operationId="indexAll",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="All Products - Publicly Accessable" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function indexAll(Request $request)
    {
        try {
            $data = $this->productRepository->getPaginatedData($request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Product List Fetched Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * @OA\GET(
     *     path="/api/products/view/search",
     *     tags={"Products"},
     *     summary="All Products - Publicly Accessable",
     *     description="All Products - Publicly Accessable",
     *     operationId="search",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", description="search, eg; Test", example="Test", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="All Products - Publicly Accessable" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function search(Request $request)
    {
        try {
            $data = $this->productRepository->searchProduct($request->search, $request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Product List Fetched Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Create New Product",
     *     description="Create New Product",
     *     operationId="store",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="title", type="string", example="Product 1"),
     *              @OA\Property(property="description", type="string", example="Description"),
     *              @OA\Property(property="price", type="integer", example=10120),
     *              @OA\Property(property="image", type="string", example=""),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Create New Product" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function store(ProductRequest $request)
{
    try {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:products,code',
            'actualPrice' => 'required|numeric',
            'sellPrice' => 'required|numeric',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
            'thumbnail' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
        }

        // Prepare data for insertion
        $data = $request->all();

        if ($request->hasFile('thumbnail')) {
            $image = $request->file('thumbnail');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $thumbnailPath = $image->move(public_path('uploads'), $filename); // Save to public/uploads
            $data['thumbnail'] = 'uploads/' . $filename; // Store relative path in DB
        }

        // Insert the data into the products table using Query Builder
        $productId = DB::table('products')->insertGetId([
            'name' => $data['name'],
            'code' => $data['code'],
            'actualPrice' => $data['actualPrice'],
            'sellPrice' => $data['sellPrice'],
            'description' => $data['description'] ?? null, // Optional description field
            'category' => $data['category'],
            'brand' => $data['brand'],
            'unit' => $data['unit'],
            'status' => 'Active',
            'user_id' => auth()->user()->id,
            'thumbnail' => $data['thumbnail'] ?? null, // Stored path from above
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Handle attributes separately if it's an array of objects
        if (!empty($data['attributes']) && is_array($data['attributes'])) {
            foreach ($data['attributes'] as $attribute) {
                DB::table('product_attributes')->insert([
                    'product_id' => $productId,
                    'label' => $attribute['label'],
                    'value' => $attribute['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Return success response
        return response()->json(['message' => 'New Product Created Successfully!', 'type' => 'success', 'productId' => $productId]);

    } catch (\Exception $exception) {
        // Return error response if something goes wrong
        return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    /**
     * @OA\GET(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Show Product Details",
     *     description="Show Product Details",
     *     operationId="show",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Show Product Details" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function show($id)
    {
        try {
            // Retrieve the product by ID
            $data = $this->productRepository->getByID($id); // Directly use the product model

            // Check if the product is found
            if (is_null($data)) {
                return $this->responseRepository->ResponseError(null, 'product Not Found', Response::HTTP_NOT_FOUND);
            }

            // Set the avatar URL
            $data->thumbnail = url($data->thumbnail);
            // Return success response with product data
            return $this->responseRepository->ResponseSuccess($data, 'product Details Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Update Product",
     *     description="Update Product",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="title", type="string", example="Product 1"),
     *              @OA\Property(property="description", type="string", example="Description"),
     *              @OA\Property(property="price", type="integer", example=10120),
     *              @OA\Property(property="image", type="string", example=""),
     *          ),
     *      ),
     *     operationId="update",
     *     @OA\Response( response=200, description="Update Product" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function update(Request $request, $id)
{
    try {
        // return response()->json($request->all()); 
        if(!empty($request->status) && !empty($id)){
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['message' => 'product not found', 'type' => 'error']); 
            }
            $product->status = $request->status;
            $saved = $product->save();

            if($saved){
                $product->avatarpath = url($product->avatar);
                return response()->json(['data'=> $product,'message' => 'product updated successfully', 'type' => 'success']);
            }else{
                return response()->json(['message' => 'product updated Failed', 'type' => 'error']);
            }
        }else{
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:100|unique:products,code,' . $id,
                'actualPrice' => 'required|numeric',
                'sellPrice' => 'required|numeric',
                'attributes' => 'nullable',
                'description' => 'nullable|string|max:1000',
                'category' => 'required|string|max:255',
                'brand' => 'nullable|string|max:255',
                'unit' => 'nullable|string|max:50',
                'thumbnail' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            // Find the existing product
            $product = DB::table('products')->where('id', $id)->first();

            if (!$product) {
                return response()->json(['message' => 'Product Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Prepare data for update
            $data = $request->all();

            // Handle thumbnail upload if new file is provided
            if ($request->hasFile('thumbnail')) {
                $image = $request->file('thumbnail');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $thumbnailPath = $image->move(public_path('uploads'), $filename); // Save to public/uploads
                $data['thumbnail'] = 'uploads/' . $filename; // Store relative path in DB
            } else {
                // If no new thumbnail is uploaded, retain the old one
                $data['thumbnail'] = $product->thumbnail;
            }

            // Update the product using Query Builder
            DB::table('products')->where('id', $id)->update([
                'name' => $data['name'],
                'code' => $data['code'],
                'actualPrice' => $data['actualPrice'],
                'sellPrice' => $data['sellPrice'],
                'description' => $data['description'] ?? null, // Optional description field
                'category' => $data['category'],
                'brand' => $data['brand'],
                'unit' => $data['unit'],
                'attributes' => $data['attributes'],
                'thumbnail' => $data['thumbnail'], // Stored path from above or old one
                'updated_at' => now(),
            ]);

            // Handle attributes separately if it's an array of objects
            if (!empty($data['attributes']) && is_array($data['attributes'])) {
                // First, delete existing attributes
                DB::table('product_attributes')->where('product_id', $id)->delete();

                // Then, insert new attributes
                foreach ($data['attributes'] as $attribute) {
                    DB::table('product_attributes')->insert([
                        'product_id' => $id,
                        'label' => $attribute['label'],
                        'value' => $attribute['value'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Return success response
            return response()->json(['message' => 'Product Updated Successfully!', 'type' => 'success']);
        }
    } catch (\Exception $exception) {
        // Return error response if something goes wrong
        return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    /**
     * @OA\DELETE(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Delete Product",
     *     description="Delete Product",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Delete Product" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function destroy($id)
    {
        try {
            $produtData =  $this->productRepository->getByID($id);
            $deleted = $this->productRepository->delete($id);
            if(!$deleted)
                return $this->responseRepository->ResponseError(null, 'Product Not Found', Response::HTTP_NOT_FOUND);

            return $this->responseRepository->ResponseSuccess($produtData, 'Product Deleted Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
