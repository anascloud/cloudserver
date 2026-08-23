<?php

namespace App\Http\Controllers\Campaigns;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use DB;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
    }

    public function indexAll(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);

            $campaigns = DB::table('campaigns')->orderBy('id', 'DESC')->paginate($perPage, ['*'], 'page', $currentPage);

            return response()->json([
                'status' => true,
                'message' => 'All Campaigns Fetched Successfully!',
                'errors' => null,
                'data' => $campaigns->items(),
            ]);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage(), $e->getLine()]);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);

            $campaigns = DB::table('campaigns')
                ->where('subject', 'LIKE', '%' . $search . '%')
                ->orWhere('company', 'LIKE', '%' . $search . '%')
                ->orWhere('service', 'LIKE', '%' . $search . '%')
                ->orderBy('id', 'DESC')
                ->paginate($perPage, ['*'], 'page', $currentPage);

            return response()->json([
                'status' => true,
                'message' => 'Campaign Search Results Fetched Successfully!',
                'errors' => null,
                'data' => $campaigns->items(),
            ]);
        } catch (\Exception $e) {
            return response()->json([$e->getMessage(), $e->getLine()]);
        }
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $currentPage = $request->input('page', 1);

            $query = DB::table('campaigns as c')
                ->select(
                    'c.id',
                    'c.subject',
                    'c.deadline',
                    'c.company',
                    'c.service',
                    'c.description',
                    'c.contact',
                    'c.source',
                    'c.type',
                    'c.status',
                    'c.created_at',
                    'c.updated_at'
                )
                ->orderBy('c.id', 'DESC');

            $campaigns = $query->paginate($perPage, ['*'], 'page', $currentPage);

            $formattedData = [];
            foreach ($campaigns as $campaign) {
                $formattedData[] = [
                    'id' => $campaign->id,
                    'subject' => $campaign->subject,
                    'deadline' => $campaign->deadline,
                    'company' => $campaign->company,
                    'service' => $campaign->service,
                    'description' => $campaign->description,
                    'contact' => $campaign->contact,
                    'source' => $campaign->source,
                    'type' => $campaign->type,
                    'status' => $campaign->status,
                    'created_at' => $campaign->created_at,
                    'updated_at' => $campaign->updated_at,
                ];
            }

            $paginationLinks = [];
            $baseUrl = $request->url();
            for ($i = 1; $i <= $campaigns->lastPage(); $i++) {
                $paginationLinks[] = [
                    'url' => $i == $currentPage ? null : $baseUrl . '?page=' . $i . '&per_page=' . $perPage,
                    'label' => $i,
                    'active' => $i == $currentPage,
                ];
            }

            return response()->json([
                'status' => true,
                'message' => 'Campaign List Fetched Successfully!',
                'errors' => null,
                'data' => [
                    'current_page' => $campaigns->currentPage(),
                    'data' => $formattedData,
                    'first_page_url' => $campaigns->url(1),
                    'from' => $campaigns->firstItem(),
                    'last_page' => $campaigns->lastPage(),
                    'last_page_url' => $campaigns->url($campaigns->lastPage()),
                    'links' => $paginationLinks,
                    'next_page_url' => $campaigns->nextPageUrl(),
                    'path' => $baseUrl,
                    'per_page' => $campaigns->perPage(),
                    'prev_page_url' => $campaigns->previousPageUrl(),
                    'to' => $campaigns->lastItem(),
                    'total' => $campaigns->total(),
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
                'subject' => 'required|string|max:255',
                'deadline' => 'required|date',
                'company' => 'required|string|max:255',
                'service' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'contact' => 'nullable|string|max:255',
                'source' => 'nullable|string|max:255',
                'type' => 'required|string|max:100',
                'status' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $data = $request->all();
            $campaignId = DB::table('campaigns')->insertGetId([
                'subject' => $data['subject'],
                'deadline' => $data['deadline'],
                'company' => $data['company'],
                'service' => $data['service'],
                'description' => $data['description'] ?? null,
                'contact' => $data['contact'] ?? null,
                'source' => $data['source'] ?? null,
                'type' => $data['type'],
                'status' => $data['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'New Campaign Created Successfully!', 'type' => 'success', 'campaignId' => $campaignId]);

        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $campaign = DB::table('campaigns')->where('id', $id)->first();

            if (!$campaign) {
                return response()->json(['message' => 'Campaign Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['data' => $campaign, 'message' => 'Campaign Details Fetched Successfully!', 'type' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject' => 'required|string|max:255',
                'deadline' => 'required|date',
                'company' => 'required|string|max:255',
                'service' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'contact' => 'nullable|string|max:255',
                'source' => 'nullable|string|max:255',
                'type' => 'required|string|max:100',
                'status' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first(), 'type' => 'error']);
            }

            $data = $request->all();
            $campaign = DB::table('campaigns')->where('id', $id)->first();

            if (!$campaign) {
                return response()->json(['message' => 'Campaign Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            DB::table('campaigns')->where('id', $id)->update([
                'subject' => $data['subject'],
                'deadline' => $data['deadline'],
                'company' => $data['company'],
                'service' => $data['service'],
                'description' => $data['description'] ?? null,
                'contact' => $data['contact'] ?? null,
                'source' => $data['source'] ?? null,
                'type' => $data['type'],
                'status' => $data['status'],
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Campaign Updated Successfully!', 'type' => 'success']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $campaign = DB::table('campaigns')->where('id', $id)->first();

            if (!$campaign) {
                return response()->json(['message' => 'Campaign Not Found', 'type' => 'error'], Response::HTTP_NOT_FOUND);
            }

            DB::table('campaigns')->where('id', $id)->delete();

            return response()->json(['message' => 'Campaign Deleted Successfully!', 'type' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'type' => 'error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
