<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
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

            $query = JournalEntry::query();

            if ($search) {
                $query->where('journal_no', 'LIKE', "%{$search}%");
            }

            $companyId = $request->input('companyId');
            if ($companyId) {
                $query->where('company_id', $companyId);
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'JournalEntrys fetched successfully!',
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
            $item = JournalEntry::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'JournalEntry fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'JournalEntry not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'journal_no' => 'sometimes|required_without:journalNo|string|max:255',
                'journalNo' => 'sometimes|required_without:journal_no|string|max:255',
                'journal_type_id' => 'sometimes|required_without:journalTypeId|integer',
                'journalTypeId' => 'sometimes|required_without:journal_type_id|integer',
                'posting_date' => 'sometimes|nullable|date',
                'postingDate' => 'sometimes|nullable|date',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'journal_template_id' => 'sometimes|nullable|integer',
                'journalTemplateId' => 'sometimes|nullable|integer',
                'reference_no' => 'sometimes|nullable|string|max:255',
                'referenceNo' => 'sometimes|nullable|string|max:255',
                'reference_date' => 'sometimes|nullable|date',
                'referenceDate' => 'sometimes|nullable|date',
                'total_debit' => 'sometimes|nullable|numeric',
                'totalDebit' => 'sometimes|nullable|numeric',
                'total_credit' => 'sometimes|nullable|numeric',
                'totalCredit' => 'sometimes|nullable|numeric',
            ]);

            $data = [
                'journal_no' => $request->input('journal_no') ?? $request->input('journalNo'),
                'journal_type_id' => $request->input('journal_type_id') ?? $request->input('journalTypeId'),
                'posting_date' => $request->input('posting_date') ?? $request->input('postingDate'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'journal_template_id' => $request->input('journal_template_id') ?? $request->input('journalTemplateId'),
                'reference_no' => $request->input('reference_no') ?? $request->input('referenceNo'),
                'reference_date' => $request->input('reference_date') ?? $request->input('referenceDate'),
                'total_debit' => $request->input('total_debit') ?? $request->input('totalDebit'),
                'total_credit' => $request->input('total_credit') ?? $request->input('totalCredit'),
            ];

            $item = JournalEntry::create($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['journal_entry_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'JournalEntry created successfully!',
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
                'journal_no' => 'sometimes|required_without:journalNo|string|max:255',
                'journalNo' => 'sometimes|required_without:journal_no|string|max:255',
                'journal_type_id' => 'sometimes|required_without:journalTypeId|integer',
                'journalTypeId' => 'sometimes|required_without:journal_type_id|integer',
                'posting_date' => 'sometimes|nullable|date',
                'postingDate' => 'sometimes|nullable|date',
                'company_id' => 'sometimes|required_without:companyId|integer',
                'companyId' => 'sometimes|required_without:company_id|integer',
                'journal_template_id' => 'sometimes|nullable|integer',
                'journalTemplateId' => 'sometimes|nullable|integer',
                'reference_no' => 'sometimes|nullable|string|max:255',
                'referenceNo' => 'sometimes|nullable|string|max:255',
                'reference_date' => 'sometimes|nullable|date',
                'referenceDate' => 'sometimes|nullable|date',
                'total_debit' => 'sometimes|nullable|numeric',
                'totalDebit' => 'sometimes|nullable|numeric',
                'total_credit' => 'sometimes|nullable|numeric',
                'totalCredit' => 'sometimes|nullable|numeric',
            ]);

            $item = JournalEntry::findOrFail($request->id);
            $data = [
                'journal_no' => $request->input('journal_no') ?? $request->input('journalNo'),
                'journal_type_id' => $request->input('journal_type_id') ?? $request->input('journalTypeId'),
                'posting_date' => $request->input('posting_date') ?? $request->input('postingDate'),
                'company_id' => $request->input('company_id') ?? $request->input('companyId'),
                'journal_template_id' => $request->input('journal_template_id') ?? $request->input('journalTemplateId'),
                'reference_no' => $request->input('reference_no') ?? $request->input('referenceNo'),
                'reference_date' => $request->input('reference_date') ?? $request->input('referenceDate'),
                'total_debit' => $request->input('total_debit') ?? $request->input('totalDebit'),
                'total_credit' => $request->input('total_credit') ?? $request->input('totalCredit'),
            ];

            $item->update($data);
            // Handle details if present
            if ($request->has('details')) {
                $item->details()->delete();
                foreach ($request->input('details') as $detail) {
                    $detail['journal_entry_id'] = $item->id;
                    $item->details()->create($detail);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'JournalEntry updated successfully!',
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
            $item = JournalEntry::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'JournalEntry deleted successfully!',
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
            JournalEntry::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'JournalEntrys deleted successfully!',
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
