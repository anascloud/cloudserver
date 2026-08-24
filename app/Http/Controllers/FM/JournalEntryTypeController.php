<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\JournalEntryType;
use Illuminate\Http\Request;

class JournalEntryTypeController extends Controller
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

            $query = JournalEntryType::query();

            if ($search) {
                $query->where('journal_type_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'JournalEntryTypes fetched successfully!',
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
            $item = JournalEntryType::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'JournalEntryType fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'JournalEntryType not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'journal_type_name' => 'sometimes|required_without:journalTypeName|string|max:255',
                'journalTypeName' => 'sometimes|required_without:journal_type_name|string|max:255',
            ]);

            $data = [
                'journal_type_name' => $request->input('journal_type_name') ?? $request->input('journalTypeName'),
            ];

            $item = JournalEntryType::create($data);

            return response()->json([
                'status' => true,
                'message' => 'JournalEntryType created successfully!',
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
                'journal_type_name' => 'sometimes|required_without:journalTypeName|string|max:255',
                'journalTypeName' => 'sometimes|required_without:journal_type_name|string|max:255',
            ]);

            $item = JournalEntryType::findOrFail($request->id);
            $data = [
                'journal_type_name' => $request->input('journal_type_name') ?? $request->input('journalTypeName'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'JournalEntryType updated successfully!',
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
            $item = JournalEntryType::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'JournalEntryType deleted successfully!',
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
            JournalEntryType::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'JournalEntryTypes deleted successfully!',
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
