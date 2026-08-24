<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\CurrencyExchange;
use Illuminate\Http\Request;

class CurrencyExchangeController extends Controller
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

            $query = CurrencyExchange::query();

            if ($search) {
                $query->where('currency_exchange_no', 'LIKE', "%{$search}%");
            }


            $items = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'CurrencyExchanges fetched successfully!',
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
            $item = CurrencyExchange::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'CurrencyExchange fetched successfully!',
                'errors' => null,
                'data' => $item,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'CurrencyExchange not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'currency_exchange_no' => 'sometimes|required|string|max:255',
                'currencyExchangeNo' => 'sometimes|required|string|max:255',
                'date_of_establishment' => 'sometimes|nullable|date',
                'dateOfEstablishment' => 'sometimes|nullable|date',
                'currency_from_id' => 'sometimes|required|integer',
                'currencyFromId' => 'sometimes|required|integer',
                'currency_to_id' => 'sometimes|required|integer',
                'currencyToId' => 'sometimes|required|integer',
                'exchange_rate' => 'sometimes|nullable|numeric',
                'exchangeRate' => 'sometimes|nullable|numeric',
                'is_purchase' => 'sometimes|boolean',
                'isPurchase' => 'sometimes|boolean',
                'is_selling' => 'sometimes|boolean',
                'isSelling' => 'sometimes|boolean',
            ]);

            $data = [
                'currency_exchange_no' => $request->input('currency_exchange_no') ?? $request->input('currencyExchangeNo'),
                'date_of_establishment' => $request->input('date_of_establishment') ?? $request->input('dateOfEstablishment'),
                'currency_from_id' => $request->input('currency_from_id') ?? $request->input('currencyFromId'),
                'currency_to_id' => $request->input('currency_to_id') ?? $request->input('currencyToId'),
                'exchange_rate' => $request->input('exchange_rate') ?? $request->input('exchangeRate'),
                'is_purchase' => $request->input('is_purchase') ?? $request->input('isPurchase'),
                'is_selling' => $request->input('is_selling') ?? $request->input('isSelling'),
            ];

            $item = CurrencyExchange::create($data);

            return response()->json([
                'status' => true,
                'message' => 'CurrencyExchange created successfully!',
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
                'currency_exchange_no' => 'sometimes|required|string|max:255',
                'currencyExchangeNo' => 'sometimes|required|string|max:255',
                'date_of_establishment' => 'sometimes|nullable|date',
                'dateOfEstablishment' => 'sometimes|nullable|date',
                'currency_from_id' => 'sometimes|required|integer',
                'currencyFromId' => 'sometimes|required|integer',
                'currency_to_id' => 'sometimes|required|integer',
                'currencyToId' => 'sometimes|required|integer',
                'exchange_rate' => 'sometimes|nullable|numeric',
                'exchangeRate' => 'sometimes|nullable|numeric',
                'is_purchase' => 'sometimes|boolean',
                'isPurchase' => 'sometimes|boolean',
                'is_selling' => 'sometimes|boolean',
                'isSelling' => 'sometimes|boolean',
            ]);

            $item = CurrencyExchange::findOrFail($request->id);
            $data = [
                'currency_exchange_no' => $request->input('currency_exchange_no') ?? $request->input('currencyExchangeNo'),
                'date_of_establishment' => $request->input('date_of_establishment') ?? $request->input('dateOfEstablishment'),
                'currency_from_id' => $request->input('currency_from_id') ?? $request->input('currencyFromId'),
                'currency_to_id' => $request->input('currency_to_id') ?? $request->input('currencyToId'),
                'exchange_rate' => $request->input('exchange_rate') ?? $request->input('exchangeRate'),
                'is_purchase' => $request->input('is_purchase') ?? $request->input('isPurchase'),
                'is_selling' => $request->input('is_selling') ?? $request->input('isSelling'),
            ];

            $item->update($data);

            return response()->json([
                'status' => true,
                'message' => 'CurrencyExchange updated successfully!',
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
            $item = CurrencyExchange::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'CurrencyExchange deleted successfully!',
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
            CurrencyExchange::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'CurrencyExchanges deleted successfully!',
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
