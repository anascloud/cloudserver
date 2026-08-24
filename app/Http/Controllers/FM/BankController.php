<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
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

            $query = Bank::query();

            if ($search) {
                $query->where('bank_name', 'LIKE', "%{$search}%");
            }

            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'Banks fetched successfully!',
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
            $item = Bank::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Bank fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Bank not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bank_name' => 'sometimes|required_without:bankName|string|max:255',
                'bankName' => 'sometimes|required_without:bank_name|string|max:255',
                'bank_website' => 'sometimes|nullable|string|max:255',
                'bankWebsite' => 'sometimes|nullable|string|max:255',
                'swift_code' => 'sometimes|nullable|string|max:255',
                'swiftCode' => 'sometimes|nullable|string|max:255',
                'routing_number' => 'sometimes|nullable|string|max:255',
                'routingNumber' => 'sometimes|nullable|string|max:255',
                'contact_number' => 'sometimes|nullable|string|max:255',
                'contactNumber' => 'sometimes|nullable|string|max:255',
            ]);

            $data = [
                'bank_name' => $request->input('bank_name') ?? $request->input('bankName'),
                'bank_website' => $request->input('bank_website') ?? $request->input('bankWebsite'),
                'swift_code' => $request->input('swift_code') ?? $request->input('swiftCode'),
                'routing_number' => $request->input('routing_number') ?? $request->input('routingNumber'),
                'contact_number' => $request->input('contact_number') ?? $request->input('contactNumber'),
            ];

            $item = Bank::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Bank created successfully!',
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
                'bank_name' => 'sometimes|required_without:bankName|string|max:255',
                'bankName' => 'sometimes|required_without:bank_name|string|max:255',
                'bank_website' => 'sometimes|nullable|string|max:255',
                'bankWebsite' => 'sometimes|nullable|string|max:255',
                'swift_code' => 'sometimes|nullable|string|max:255',
                'swiftCode' => 'sometimes|nullable|string|max:255',
                'routing_number' => 'sometimes|nullable|string|max:255',
                'routingNumber' => 'sometimes|nullable|string|max:255',
                'contact_number' => 'sometimes|nullable|string|max:255',
                'contactNumber' => 'sometimes|nullable|string|max:255',
            ]);

            $item = Bank::findOrFail($request->id);
            $data = [
                'bank_name' => $request->input('bank_name') ?? $request->input('bankName'),
                'bank_website' => $request->input('bank_website') ?? $request->input('bankWebsite'),
                'swift_code' => $request->input('swift_code') ?? $request->input('swiftCode'),
                'routing_number' => $request->input('routing_number') ?? $request->input('routingNumber'),
                'contact_number' => $request->input('contact_number') ?? $request->input('contactNumber'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Bank updated successfully!',
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
            $item = Bank::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Bank deleted successfully!',
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
            Bank::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Banks deleted successfully!',
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
