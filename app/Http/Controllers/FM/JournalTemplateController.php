<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\JournalTemplate;
use Illuminate\Http\Request;

class JournalTemplateController extends Controller
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

            $query = JournalTemplate::query();

            if ($search) {
                $query->where('journal_template_title', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'JournalTemplates fetched successfully!',
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
            $item = JournalTemplate::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'JournalTemplate fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'JournalTemplate not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'journal_template_title' => 'sometimes|required_without:journalTemplateTitle|string|max:255',
                'journalTemplateTitle' => 'sometimes|required_without:journal_template_title|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'journal_type_id' => 'sometimes|required_without:journalTypeId|integer',
                'journalTypeId' => 'sometimes|required_without:journal_type_id|integer',
            ]);

            $data = [
                'journal_template_title' => $request->input('journal_template_title') ?? $request->input('journalTemplateTitle'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'journal_type_id' => $request->input('journal_type_id') ?? $request->input('journalTypeId'),
            ];

            $item = JournalTemplate::create($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['journal_template_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'JournalTemplate created successfully!',
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
                'journal_template_title' => 'sometimes|required_without:journalTemplateTitle|string|max:255',
                'journalTemplateTitle' => 'sometimes|required_without:journal_template_title|string|max:255',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'journal_type_id' => 'sometimes|required_without:journalTypeId|integer',
                'journalTypeId' => 'sometimes|required_without:journal_type_id|integer',
            ]);

            $item = JournalTemplate::findOrFail($request->id);
            $data = [
                'journal_template_title' => $request->input('journal_template_title') ?? $request->input('journalTemplateTitle'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'journal_type_id' => $request->input('journal_type_id') ?? $request->input('journalTypeId'),
            ];

            $item->update($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['journal_template_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'JournalTemplate updated successfully!',
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
            $item = JournalTemplate::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'JournalTemplate deleted successfully!',
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
            JournalTemplate::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'JournalTemplates deleted successfully!',
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
