<?php

namespace App\Http\Controllers\Leads;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use DB;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);

            $query = DB::table('leads as l')
                ->select(
                    'l.id',
                    'l.fullName',
                    'l.email',
                    'l.phone',
                    'l.address',
                    'l.company',
                    'l.assignedTo',
                    'l.region',
                    'l.feedback',
                    'l.industry',
                    'l.title',
                    'l.leadStatus',
                    'l.description',
                    'l.created_at',
                    'l.updated_at'
                )
                ->orderBy('l.id', 'DESC');

            $leads = $query->paginate($perPage, ['*'], 'page', $currentPage);

            $formattedData = [];
            foreach ($leads as $lead) {
                $formattedData[] = [
                    'id' => $lead->id,
                    'fullName' => $lead->fullName,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'address' => $lead->address,
                    'company' => $lead->company,
                    'assignedTo' => $lead->assignedTo,
                    'region' => $lead->region,
                    'feedback' => $lead->feedback,
                    'industry' => $lead->industry,
                    'title' => $lead->title,
                    'leadStatus' => $lead->leadStatus,
                    'description' => $lead->description,
                    'created_at' => $lead->created_at,
                    'updated_at' => $lead->updated_at,
                ];
            }

            $paginationLinks = [];
            $baseUrl = $request->url();
            for ($i = 1; $i <= $leads->lastPage(); $i++) {
                $paginationLinks[] = [
                    'url' => $i == $currentPage ? null : $baseUrl . '?page=' . $i . '&per_page=' . $perPage,
                    'label' => $i,
                    'active' => $i == $currentPage,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Lead List Fetched Successfully!',
                'errors' => null,
                'data' => [
                    'current_page' => $leads->currentPage(),
                    'data' => $formattedData,
                    'first_page_url' => $leads->url(1),
                    'from' => $leads->firstItem(),
                    'last_page' => $leads->lastPage(),
                    'last_page_url' => $leads->url($leads->lastPage()),
                    'links' => $paginationLinks,
                    'next_page_url' => $leads->nextPageUrl(),
                    'path' => $baseUrl,
                    'per_page' => $leads->perPage(),
                    'prev_page_url' => $leads->previousPageUrl(),
                    'to' => $leads->lastItem(),
                    'total' => $leads->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage(), $e->getLine()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fullName' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string|max:255',
                'company' => 'required|string|max:255',
                'assignedTo' => 'nullable|string|max:255',
                'region' => 'nullable|string|max:100',
                'feedback' => 'nullable|string|max:500',
                'industry' => 'nullable|string|max:100',
                'title' => 'nullable|string|max:100',
                'leadStatus' => 'required|string|max:50',
                'description' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $data = $request->all();
            $leadId = DB::table('leads')->insertGetId([
                'fullName' => $data['fullName'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'company' => $data['company'],
                'assignedTo' => $data['assignedTo'] ?? null,
                'region' => $data['region'] ?? null,
                'feedback' => $data['feedback'] ?? null,
                'industry' => $data['industry'] ?? null,
                'title' => $data['title'] ?? null,
                'leadStatus' => $data['leadStatus'],
                'description' => $data['description'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'New Lead Created Successfully!', 'type' => 'success', 'leadId' => $leadId]);

        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $lead = DB::table('leads')->where('id', $id)->first();

            if (!$lead) {
                return response()->json(['message' => 'Lead Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['data' => $lead, 'message' => 'Lead Details Fetched Successfully!', 'type' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fullName' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string|max:255',
                'company' => 'required|string|max:255',
                'assignedTo' => 'nullable|string|max:255',
                'region' => 'nullable|string|max:100',
                'feedback' => 'nullable|string|max:500',
                'industry' => 'nullable|string|max:100',
                'title' => 'nullable|string|max:100',
                'leadStatus' => 'required|string|max:50',
                'description' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $data = $request->all();
            $lead = DB::table('leads')->where('id', $id)->first();

            if (!$lead) {
                return response()->json(['message' => 'Lead Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            DB::table('leads')->where('id', $id)->update([
                'fullName' => $data['fullName'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'] ?? null,
                'company' => $data['company'],
                'assignedTo' => $data['assignedTo'] ?? null,
                'region' => $data['region'] ?? null,
                'feedback' => $data['feedback'] ?? null,
                'industry' => $data['industry'] ?? null,
                'title' => $data['title'] ?? null,
                'leadStatus' => $data['leadStatus'],
                'description' => $data['description'] ?? null,
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Lead Updated Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $lead = DB::table('leads')->where('id', $id)->first();

            if (!$lead) {
                return response()->json(['message' => 'Lead Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            DB::table('leads')->where('id', $id)->delete();

            return response()->json(['message' => 'Lead Deleted Successfully!', 'type' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}