<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Repositories\ResponseRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    public $responseRepository;

    public function __construct(ResponseRepository $rp)
    {
        $this->middleware('auth:api', ['except' => ['indexAll']]);
        $this->responseRepository = $rp;
    }

    /* ---------------------------------------------------------------------
     |  INDEX
     |---------------------------------------------------------------------*/
    /**
     * @OA\GET(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Get Task List",
     *     operationId="index",
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=10)),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="OK"),
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $query = DB::table('tasks as t')
                ->leftJoin('users as u', 't.assigned_to', '=', 'u.id')
                ->select(
                    't.id', 't.subject', 't.ticket_id', 't.assigned_to',
                    't.start_date', 't.due_date', 't.priority', 't.status',
                    't.description', 't.file_path', 't.created_at', 't.updated_at',
                    'u.name as assigned_to_name'
                )
                ->orderByDesc('t.id');

            if ($request->filled('status')) {
                $query->where('t.status', $request->status);
            }

            $tasks = $query->paginate($perPage);

            $formatted = collect($tasks->items())->map(fn ($t) => [
                'id'              => $t->id,
                'subject'         => $t->subject,
                'ticketId'        => $t->ticket_id,
                'assignedTo'      => $t->assigned_to,
                'assignedToName'  => $t->assigned_to_name,
                'startDate'       => $t->start_date,
                'dueDate'         => $t->due_date,
                'priority'        => $t->priority,
                'status'          => $t->status,
                'description'     => $t->description,
                'file'            => $t->file_path ? url($t->file_path) : null,
                'createdAt'       => $t->created_at,
                'updatedAt'       => $t->updated_at,
            ]);

            return $this->responseRepository->ResponseSuccess([
                'current_page' => $tasks->currentPage(),
                'data'         => $formatted,
                'last_page'    => $tasks->lastPage(),
                'per_page'     => $tasks->perPage(),
                'total'        => $tasks->total(),
            ], 'Task List Fetched Successfully!');
        } catch (\Throwable $e) {
            return $this->responseRepository->ResponseError(
                null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /* ---------------------------------------------------------------------
     |  STORE
     |---------------------------------------------------------------------*/
    /**
     * @OA\POST(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Create New Task",
     *     operationId="store",
     *     @OA\RequestBody(@OA\JsonContent(type="object")),
     *     @OA\Response(response=200, description="Create New Task"),
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return $this->responseRepository->ResponseError(
                null, $validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $data = $this->mapPayload($request);

            $taskId = DB::table('tasks')->insertGetId(array_merge($data, [
                'user_id'    => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            return $this->responseRepository->ResponseSuccess(
                ['id' => $taskId], 'Task Created Successfully!'
            );
        } catch (\Throwable $e) {
            return $this->responseRepository->ResponseError(
                null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /* ---------------------------------------------------------------------
     |  SHOW
     |---------------------------------------------------------------------*/
    /**
     * @OA\GET(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Show Task Details",
     *     operationId="show",
     *     @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK"),
     * )
     */
    public function show($id)
    {
        $task = DB::table('tasks as t')
            ->leftJoin('users as u', 't.assigned_to', '=', 'u.id')
            ->select('t.*', 'u.name as assigned_to_name')
            ->where('t.id', $id)
            ->first();

        if (!$task) {
            return $this->responseRepository->ResponseError(
                null, 'Task Not Found', Response::HTTP_NOT_FOUND
            );
        }

        $task->file = $task->file_path ? url($task->file_path) : null;
        $task->assignedToName = $task->assigned_to_name;
        unset($task->file_path, $task->assigned_to_name);

        return $this->responseRepository->ResponseSuccess($task, 'Task Fetched Successfully!');
    }

    /* ---------------------------------------------------------------------
     |  UPDATE
     |---------------------------------------------------------------------*/
    /**
     * @OA\PUT(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Update Task",
     *     operationId="update",
     *     @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(type="object")),
     *     @OA\Response(response=200, description="Updated"),
     * )
     */
    public function update(Request $request, $id)
    {
        $task = DB::table('tasks')->where('id', $id)->first();
        if (!$task) {
            return $this->responseRepository->ResponseError(
                null, 'Task Not Found', Response::HTTP_NOT_FOUND
            );
        }

        // ── Fast path: status-only update (inline status dropdown in drawer)
        if ($request->filled('status') && $request->keys() === ['status']) {
            DB::table('tasks')->where('id', $id)->update([
                'status'     => $request->status,
                'updated_at' => now(),
            ]);
            return $this->responseRepository->ResponseSuccess(
                ['id' => $id], 'Task Updated Successfully!'
            );
        }

        // ── Full update
        $validator = Validator::make($request->all(), $this->rules($id));
        if ($validator->fails()) {
            return $this->responseRepository->ResponseError(
                null, $validator->errors()->first(), Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $data = $this->mapPayload($request, $task->file_path);

            DB::table('tasks')->where('id', $id)->update(array_merge($data, [
                'updated_at' => now(),
            ]));

            return $this->responseRepository->ResponseSuccess(
                ['id' => $id], 'Task Updated Successfully!'
            );
        } catch (\Throwable $e) {
            return $this->responseRepository->ResponseError(
                null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /* ---------------------------------------------------------------------
     |  DESTROY
     |---------------------------------------------------------------------*/
    /**
     * @OA\DELETE(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Delete Task",
     *     operationId="destroy",
     *     @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Deleted"),
     * )
     */
    public function destroy($id)
    {
        $task = DB::table('tasks')->where('id', $id)->first();
        if (!$task) {
            return $this->responseRepository->ResponseError(
                null, 'Task Not Found', Response::HTTP_NOT_FOUND
            );
        }

        if ($task->file_path && Storage::disk('public')->exists($task->file_path)) {
            Storage::disk('public')->delete($task->file_path);
        }

        DB::table('tasks')->where('id', $id)->delete();

        return $this->responseRepository->ResponseSuccess(
            ['id' => $id], 'Task Deleted Successfully!'
        );
    }

    /* ---------------------------------------------------------------------
     |  HELPERS
     |---------------------------------------------------------------------*/
    private function rules(?int $ignoreId = null): array
    {
        return [
            'Subject'         => 'required|string|max:255',
            'TicketId'        => 'nullable|integer|exists:tickets,id',
            'AssignedTo'      => 'nullable|integer|exists:users,id',
            'start_date'      => 'nullable|date',
            'DueDate'         => 'nullable|date|after_or_equal:start_date',
            'Priority'        => 'nullable|in:low,medium,high,urgent',
            'Description'     => 'nullable|string|max:5000',
            'Status'          => 'nullable|in:pending,in_progress,completed,cancelled',
            'RepeatDate'      => 'nullable|date',
            'Reminder'        => 'nullable|date',
            'CampaignId'      => 'nullable|integer|exists:campaigns,id',
            'LeadId'          => 'nullable|integer|exists:leads,id',
            'OpportunityId'   => 'nullable|integer|exists:opportunities,id',
            'SalesOrdersId'   => 'nullable|integer|exists:sales_orders,id',
            'InvoiceId'       => 'nullable|integer|exists:invoices,id',
            'QuotationId'     => 'nullable|integer|exists:quotations,id',
            'File'            => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ];
    }

    /**
     * Map the incoming (TitleCase) payload to snake_case DB columns,
     * uploading the file to the public disk if present.
     */
    private function mapPayload(Request $request, ?string $existingFile = null): array
    {
        $filePath = $existingFile;

        if ($request->hasFile('File')) {
            $filePath = $request->file('File')->store('uploads/tasks', 'public');
        }

        return [
            'subject'        => $request->input('Subject'),
            'ticket_id'      => $request->input('TicketId'),
            'assigned_to'    => $request->input('AssignedTo'),
            'start_date'     => $request->input('start_date'),
            'due_date'       => $request->input('DueDate'),
            'priority'       => $request->input('Priority'),
            'description'    => $request->input('Description'),
            'status'         => $request->input('Status', 'pending'),
            'repeat_date'    => $request->input('RepeatDate'),
            'reminder'       => $request->input('Reminder'),
            'campaign_id'    => $request->input('CampaignId'),
            'lead_id'        => $request->input('LeadId'),
            'opportunity_id' => $request->input('OpportunityId'),
            'sales_order_id' => $request->input('SalesOrdersId'),
            'invoice_id'     => $request->input('InvoiceId'),
            'quotation_id'   => $request->input('QuotationId'),
            'file_path'      => $filePath,
        ];
    }
}
