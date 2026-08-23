<?php

namespace App\Http\Controllers\FM;

use App\Http\Controllers\Controller;
use App\Models\FM\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
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

            $query = Company::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone_number', 'LIKE', "%{$search}%")
                      ->orWhere('domain', 'LIKE', "%{$search}%");
                });
            }

            $companies = $query->orderBy('id', 'DESC')->paginate($pageSize);

            return response()->json([
                'status' => true,
                'message' => 'Companies fetched successfully!',
                'errors' => null,
                'data' => [
                    'pageIndex' => $companies->currentPage(),
                    'pageSize' => (int) $pageSize,
                    'count' => $companies->total(),
                    'data' => $companies->items(),
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
            $company = Company::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Company fetched successfully!',
                'errors' => null,
                'data' => $company,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Company not found',
                'errors' => null,
                'data' => null,
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
            ]);

            $company = Company::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Company created successfully!',
                'errors' => null,
                'data' => $company,
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
            $company = Company::findOrFail($request->id);
            $company->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Company updated successfully!',
                'errors' => null,
                'data' => $company,
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
            $company = Company::findOrFail($id);
            $company->delete();

            return response()->json([
                'status' => true,
                'message' => 'Company deleted successfully!',
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
            Company::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Companies deleted successfully!',
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
