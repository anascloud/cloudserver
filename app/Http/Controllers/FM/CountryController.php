<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
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

            $query = Country::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('country_name', 'LIKE', "%{$search}%")
                      ->orWhere('country_code', 'LIKE', "%{$search}%");
                });
            }

            $countries = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'Country List Fetched Successfully!',
                'errors' => null,
                'data' => [
                    'pageIndex' => $countries->currentPage(),
                    'pageSize' => (int) $pageSize,
                    'count' => $countries->total(),
                    'data' => $countries->items(),
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
            $country = Country::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Country fetched successfully!',
                'errors' => null,
                'data' => $country,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Country not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'countryName' => 'required|string|max:255',
                'countryCode' => 'nullable|string|max:10',
                'dateFormat' => 'nullable|string|max:50',
                'timeFormat' => 'nullable|string|max:50',
                'timeZone' => 'nullable|string|max:100',
            ]);

            $country = Country::create([
                'country_name' => $request->countryName,
                'country_code' => $request->countryCode,
                'date_format' => $request->dateFormat,
                'time_format' => $request->timeFormat,
                'time_zone' => $request->timeZone,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Country created successfully!',
                'errors' => null,
                'data' => $country,
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
            $country = Country::findOrFail($request->id);

            $validated = $request->validate([
                'countryName' => 'required|string|max:255',
                'countryCode' => 'nullable|string|max:10',
                'dateFormat' => 'nullable|string|max:50',
                'timeFormat' => 'nullable|string|max:50',
                'timeZone' => 'nullable|string|max:100',
            ]);

            $country->update([
                'country_name' => $request->countryName,
                'country_code' => $request->countryCode,
                'date_format' => $request->dateFormat,
                'time_format' => $request->timeFormat,
                'time_zone' => $request->timeZone,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Country updated successfully!',
                'errors' => null,
                'data' => $country,
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
            $country = Country::findOrFail($id);
            $country->delete();

            return response()->json([
                'status' => true,
                'message' => 'Country deleted successfully!',
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
            Country::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Countries deleted successfully!',
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
