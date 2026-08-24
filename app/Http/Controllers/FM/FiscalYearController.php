<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\FiscalYear;
use Illuminate\Http\Request;

class FiscalYearController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
    }

    public function indexAll(Request $request)
    {
        try {
            $pageSize = $request->input('pageSize', $request->input('per_page', 10));
            $search = $request->input('search', '');

            $query = FiscalYear::query();

            if ($search) {
                $query->where('year_range', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'FiscalYears fetched successfully!',
                'errors' => null,
                'data' => [
                    'current_page' => $items->currentPage(),
                    'data' => $items->items(),
                    'first_page_url' => $items->url(1),
                    'from' => $items->firstItem(),
                    'last_page' => $items->lastPage(),
                    'last_page_url' => $items->url($items->lastPage()),
                    'next_page_url' => $items->nextPageUrl(),
                    'path' => $request->url(),
                    'per_page' => $items->perPage(),
                    'prev_page_url' => $items->previousPageUrl(),
                    'to' => $items->lastItem(),
                    'total' => $items->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    public function index(Request $request)
    {
        return $this->indexAll($request);
    }

    public function show($id)
    {
        try {
            $item = FiscalYear::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'FiscalYear fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'FiscalYear not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'year_range' => 'sometimes|required_without:yearRange|string|max:255',
                'yearRange' => 'sometimes|required_without:year_range|string|max:255',
                'start_date' => 'sometimes|nullable|date',
                'startDate' => 'sometimes|nullable|date',
                'end_date' => 'sometimes|nullable|date',
                'endDate' => 'sometimes|nullable|date',
            ]);

            $data = [
                'year_range' => $request->input('year_range') ?? $request->input('yearRange'),
                'start_date' => $request->input('start_date') ?? $request->input('startDate'),
                'end_date' => $request->input('end_date') ?? $request->input('endDate'),
            ];

            $item = FiscalYear::create($data);

            return response()->json([
                'status' => true,
                'message' => 'FiscalYear created successfully!',
                'errors' => null,
                'data' => $item,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 422);
        }
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'year_range' => 'sometimes|required_without:yearRange|string|max:255',
                'yearRange' => 'sometimes|required_without:year_range|string|max:255',
                'start_date' => 'sometimes|nullable|date',
                'startDate' => 'sometimes|nullable|date',
                'end_date' => 'sometimes|nullable|date',
                'endDate' => 'sometimes|nullable|date',
            ]);

            $item = FiscalYear::findOrFail($request->id);
            $data = [
                'year_range' => $request->input('year_range') ?? $request->input('yearRange'),
                'start_date' => $request->input('start_date') ?? $request->input('startDate'),
                'end_date' => $request->input('end_date') ?? $request->input('endDate'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'FiscalYear updated successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $item = FiscalYear::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'FiscalYear deleted successfully!',
                'errors' => null,
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->all();
            FiscalYear::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'FiscalYears deleted successfully!',
                'errors' => null,
                'data' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'errors' => null,
                'data' => null,
            ], 500);
        }
    }
}
