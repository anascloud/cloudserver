<?php

namespace App\Http\Controllers\Feedbacks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Repositories\FeedbackRepository;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class FeedbacksController extends Controller
{
    public $feedbackRepository;
    public $responseRepository;

    public function __construct(FeedbackRepository $feedbackRepository, ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->feedbackRepository = $feedbackRepository;
        $this->responseRepository = $rp;
    }

    /**
     * @OA\GET(
     *     path="/api/feedbacks",
     *     tags={"Feedbacks"},
     *     summary="Get Feedback List",
     *     description="Get Feedback List as Array",
     *     operationId="index",
     *     @OA\Response(response=200, description="Get Feedback List as Array"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function index()
    {
        try {
            // Using query builder to fetch all feedbacks
            $data = DB::table('feedbacks')->orderBy('id', 'desc')->paginate(10); // Adjust pagination as needed
            
            return $this->responseRepository->ResponseSuccess($data, 'Feedback List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/feedbacks/view/all",
     *     tags={"Feedbacks"},
     *     summary="All Feedbacks - Publicly Accessible",
     *     description="All Feedbacks - Publicly Accessible",
     *     operationId="indexAll",
     *     @OA\Parameter(name="perPage", description="perPage, eg; 20", example=20, in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="All Feedbacks - Publicly Accessible" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function indexAll(Request $request)
    {
        try {
            $data = $this->feedbackRepository->getPaginatedData($request->perPage);
            return $this->responseRepository->ResponseSuccess($data, 'Feedback List Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/feedbacks",
     *     tags={"Feedbacks"},
     *     summary="Create New Feedback",
     *     description="Create New Feedback",
     *     operationId="store",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="title", type="string", example="Feedback Title"),
     *              @OA\Property(property="product", type="string", example="Product Name"),
     *              @OA\Property(property="reference", type="string", example="Reference Code"),
     *              @OA\Property(property="description", type="string", example="Feedback Description"),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Create New Feedback" ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'product' => 'required|string',
                'reference' => 'required|string',
                'description' => 'required|string',
            ]);

            // Check for validation failures
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']); 
            }

            // Create a new feedback using the validated data
            $feedback = new Feedback();
            $feedback->title = $request->title;
            $feedback->product = $request->product;
            $feedback->reference = $request->reference;
            $feedback->description = $request->description;
            $feedback->save();

            return response()->json(['message' => 'New Feedback Created Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/feedbacks/{id}",
     *     tags={"Feedbacks"},
     *     summary="Show Feedback Details",
     *     description="Show Feedback Details",
     *     operationId="show",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Show Feedback Details" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function show($id)
    {
        try {
            $data = $this->feedbackRepository->getByID($id);
            if (is_null($data)) {
                return $this->responseRepository->ResponseError(null, 'Feedback Not Found', Response::HTTP_NOT_FOUND);
            }

            return $this->responseRepository->ResponseSuccess($data, 'Feedback Details Fetched Successfully!');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/feedbacks/{id}",
     *     tags={"Feedbacks"},
     *     summary="Update Feedback",
     *     description="Update Feedback",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="title", type="string", example="Updated Feedback Title"),
     *              @OA\Property(property="product", type="string", example="Updated Product Name"),
     *              @OA\Property(property="reference", type="string", example="Updated Reference Code"),
     *              @OA\Property(property="description", type="string", example="Updated Feedback Description"),
     *          ),
     *      ),
     *     operationId="update",
     *     @OA\Response( response=200, description="Update Feedback" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the input data
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'product' => 'required|string',
                'reference' => 'required|string',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            // Find the feedback by ID
            $feedback = Feedback::find($id);

            if (is_null($feedback)) {
                return response()->json(['message' => 'Feedback Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Update the feedback with new data
            $feedback->title = $request->title;
            $feedback->product = $request->product;
            $feedback->reference = $request->reference;
            $feedback->description = $request->description;
            $feedback->save();

            return response()->json(['message' => 'Feedback Updated Successfully.', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/feedbacks/{id}",
     *     tags={"Feedbacks"},
     *     summary="Delete Feedback",
     *     description="Delete Feedback",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", description="id, eg; 1", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Delete Feedback" ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function destroy($id)
    {
        try {
            // Find the feedback by ID
            $feedback = Feedback::find($id);

            if (is_null($feedback)) {
                return response()->json(['message' => 'Feedback Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            // Delete the feedback
            $feedback->delete();

            return response()->json(['message' => 'Feedback Deleted Successfully', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
