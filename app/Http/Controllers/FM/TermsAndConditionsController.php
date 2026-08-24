<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\TermsAndConditions;
use Illuminate\Http\Request;

class TermsAndConditionsController extends Controller
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

            $query = TermsAndConditions::query();

            if ($search) {
                $query->where('terms_and_condition_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'TermsAndConditionss fetched successfully!',
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
            $item = TermsAndConditions::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'TermsAndConditions fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'TermsAndConditions not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'terms_and_condition_name' => 'sometimes|required_without:termsAndConditionName|string|max:255',
                'termsAndConditionName' => 'sometimes|required_without:terms_and_condition_name|string|max:255',
                'is_disabled' => 'sometimes|boolean',
                'isDisabled' => 'sometimes|boolean',
                'is_selling' => 'sometimes|boolean',
                'isSelling' => 'sometimes|boolean',
                'is_buying' => 'sometimes|boolean',
                'isBuying' => 'sometimes|boolean',
            ]);

            $data = [
                'terms_and_condition_name' => $request->input('terms_and_condition_name') ?? $request->input('termsAndConditionName'),
                'is_disabled' => $request->input('is_disabled') ?? $request->input('isDisabled'),
                'is_selling' => $request->input('is_selling') ?? $request->input('isSelling'),
                'is_buying' => $request->input('is_buying') ?? $request->input('isBuying'),
            ];

            $item = TermsAndConditions::create($data);

            return response()->json([
                'status' => true,
                'message' => 'TermsAndConditions created successfully!',
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
                'terms_and_condition_name' => 'sometimes|required_without:termsAndConditionName|string|max:255',
                'termsAndConditionName' => 'sometimes|required_without:terms_and_condition_name|string|max:255',
                'is_disabled' => 'sometimes|boolean',
                'isDisabled' => 'sometimes|boolean',
                'is_selling' => 'sometimes|boolean',
                'isSelling' => 'sometimes|boolean',
                'is_buying' => 'sometimes|boolean',
                'isBuying' => 'sometimes|boolean',
            ]);

            $item = TermsAndConditions::findOrFail($request->id);
            $data = [
                'terms_and_condition_name' => $request->input('terms_and_condition_name') ?? $request->input('termsAndConditionName'),
                'is_disabled' => $request->input('is_disabled') ?? $request->input('isDisabled'),
                'is_selling' => $request->input('is_selling') ?? $request->input('isSelling'),
                'is_buying' => $request->input('is_buying') ?? $request->input('isBuying'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'TermsAndConditions updated successfully!',
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
            $item = TermsAndConditions::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'TermsAndConditions deleted successfully!',
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
            TermsAndConditions::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'TermsAndConditionss deleted successfully!',
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
