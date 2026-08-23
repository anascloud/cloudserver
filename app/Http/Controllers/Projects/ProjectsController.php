<?php

namespace App\Http\Controllers\Projects;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use DB;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
    }

    /**
     * @OA\GET(
     *     path="/api/projects",
     *     tags={"Projects"},
     *     summary="Get Project List",
     *     description="Get paginated project list",
     *     operationId="index",
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer"), example=10),
     *     @OA\Parameter(name="page", in="query", @OA\Schema(type="integer"), example=1),
     *     @OA\Response(response=200, description="Project List Fetched Successfully"),
     *     @OA\Response(response=400, description="Bad request"),
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);

            $query = DB::table('projects')
                ->select('id', 'title', 'category', 'description', 'image', 'link', 'created_at', 'updated_at')
                ->orderBy('id', 'DESC');

            $projects = $query->paginate($perPage, ['*'], 'page', $currentPage);

            $formattedData = [];
            foreach ($projects as $project) {
                $formattedData[] = [
                    'id' => $project->id,
                    'title' => $project->title,
                    'category' => $project->category,
                    'description' => $project->description,
                    'image' => $project->image,
                    'link' => $project->link,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                ];
            }

            $paginationLinks = [];
            $baseUrl = $request->url();
            for ($i = 1; $i <= $projects->lastPage(); $i++) {
                $paginationLinks[] = [
                    'url' => $i == $currentPage ? null : $baseUrl . '?page=' . $i . '&per_page=' . $perPage,
                    'label' => $i,
                    'active' => $i == $currentPage,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Project List Fetched Successfully!',
                'errors' => null,
                'data' => [
                    'current_page' => $projects->currentPage(),
                    'data' => $formattedData,
                    'first_page_url' => $projects->url(1),
                    'from' => $projects->firstItem(),
                    'last_page' => $projects->lastPage(),
                    'last_page_url' => $projects->url($projects->lastPage()),
                    'links' => $paginationLinks,
                    'next_page_url' => $projects->nextPageUrl(),
                    'path' => $baseUrl,
                    'per_page' => $projects->perPage(),
                    'prev_page_url' => $projects->previousPageUrl(),
                    'to' => $projects->lastItem(),
                    'total' => $projects->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage(), $e->getLine()]);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/projects/view/all",
     *     tags={"Projects"},
     *     summary="All Projects - Publicly Accessible",
     *     description="Get all projects without auth",
     *     operationId="indexAll",
     *     @OA\Response(response=200, description="All Projects Fetched Successfully"),
     * )
     */
    public function indexAll(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);

            $query = DB::table('projects')
                ->select('id', 'title', 'category', 'description', 'image', 'link', 'created_at', 'updated_at')
                ->orderBy('id', 'DESC');

            $projects = $query->paginate($perPage, ['*'], 'page', $currentPage);

            $formattedData = [];
            foreach ($projects as $project) {
                $formattedData[] = [
                    'id' => $project->id,
                    'title' => $project->title,
                    'category' => $project->category,
                    'description' => $project->description,
                    'image' => $project->image,
                    'link' => $project->link,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'All Projects Fetched Successfully!',
                'errors' => null,
                'data' => $formattedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage(), $e->getLine()]);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/projects/view/search",
     *     tags={"Projects"},
     *     summary="Search Projects",
     *     description="Search projects by title, category, or description",
     *     operationId="search",
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), example="Pouchao"),
     *     @OA\Response(response=200, description="Search results"),
     * )
     */
    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);

            $query = DB::table('projects')
                ->select('id', 'title', 'category', 'description', 'image', 'link', 'created_at', 'updated_at')
                ->where('title', 'LIKE', '%' . $search . '%')
                ->orWhere('category', 'LIKE', '%' . $search . '%')
                ->orWhere('description', 'LIKE', '%' . $search . '%')
                ->orderBy('id', 'DESC');

            $projects = $query->paginate($perPage, ['*'], 'page', $currentPage);

            $formattedData = [];
            foreach ($projects as $project) {
                $formattedData[] = [
                    'id' => $project->id,
                    'title' => $project->title,
                    'category' => $project->category,
                    'description' => $project->description,
                    'image' => $project->image,
                    'link' => $project->link,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Project Search Results Fetched Successfully!',
                'errors' => null,
                'data' => $formattedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage(), $e->getLine()]);
        }
    }

    /**
     * @OA\POST(
     *     path="/api/projects",
     *     tags={"Projects"},
     *     summary="Create New Project",
     *     description="Create a new project entry",
     *     operationId="store",
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="title", type="string", example="Pouchao"),
     *              @OA\Property(property="category", type="string", example="Car-Rental"),
     *              @OA\Property(property="description", type="string", example="Car Rental Booking System"),
     *              @OA\Property(property="image", type="string", example="https://example.com/image.png"),
     *              @OA\Property(property="link", type="string", example="https://example.com/project"),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="New Project Created Successfully"),
     *      @OA\Response(response=400, description="Bad request"),
     * )
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'image' => 'nullable|string|max:500',
                'link' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $data = $request->all();
            $projectId = DB::table('projects')->insertGetId([
                'title' => $data['title'],
                'category' => $data['category'],
                'description' => $data['description'] ?? null,
                'image' => $data['image'] ?? null,
                'link' => $data['link'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'New Project Created Successfully!', 'type' => 'success', 'projectId' => $projectId]);

        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\GET(
     *     path="/api/projects/{id}",
     *     tags={"Projects"},
     *     summary="Show Project Details",
     *     description="Get project by ID",
     *     operationId="show",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *     @OA\Response(response=200, description="Project Details Fetched Successfully"),
     *     @OA\Response(response=404, description="Project Not Found"),
     * )
     */
    public function show($id)
    {
        try {
            $project = DB::table('projects')->where('id', $id)->first();

            if (!$project) {
                return response()->json(['message' => 'Project Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['data' => $project, 'message' => 'Project Details Fetched Successfully!', 'type' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\PUT(
     *     path="/api/projects/{id}",
     *     tags={"Projects"},
     *     summary="Update Project",
     *     description="Update project by ID",
     *     operationId="update",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="title", type="string", example="Pouchao"),
     *              @OA\Property(property="category", type="string", example="Car-Rental"),
     *              @OA\Property(property="description", type="string", example="Car Rental Booking System"),
     *              @OA\Property(property="image", type="string", example="https://example.com/image.png"),
     *              @OA\Property(property="link", type="string", example="https://example.com/project"),
     *          ),
     *      ),
     *     @OA\Response(response=200, description="Project Updated Successfully"),
     *     @OA\Response(response=404, description="Project Not Found"),
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'description' => 'nullable|string|max:5000',
                'image' => 'nullable|string|max:500',
                'link' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $project = DB::table('projects')->where('id', $id)->first();

            if (!$project) {
                return response()->json(['message' => 'Project Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            $data = $request->all();
            DB::table('projects')->where('id', $id)->update([
                'title' => $data['title'],
                'category' => $data['category'],
                'description' => $data['description'] ?? null,
                'image' => $data['image'] ?? null,
                'link' => $data['link'] ?? null,
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Project Updated Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/projects/{id}",
     *     tags={"Projects"},
     *     summary="Delete Project",
     *     description="Delete project by ID",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *     @OA\Response(response=200, description="Project Deleted Successfully"),
     *     @OA\Response(response=404, description="Project Not Found"),
     * )
     */
    public function destroy($id)
    {
        try {
            $project = DB::table('projects')->where('id', $id)->first();

            if (!$project) {
                return response()->json(['message' => 'Project Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            DB::table('projects')->where('id', $id)->delete();

            return response()->json(['message' => 'Project Deleted Successfully!', 'type' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
