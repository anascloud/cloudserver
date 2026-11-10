<?php

namespace App\Http\Controllers;

use App\Repositories\ResponseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

abstract class ApiCrudController extends Controller
{
    protected $model;

    protected $dbTable;

    protected $fields = [];

    protected $timestamps = true;

    protected $clearMissingOnUpdate = false;

    protected $resource = 'Record';

    protected $resourcePlural;

    protected $searchable = [];

    protected $searchOnList = true;

    protected $filters = [];

    protected $rules = [];

    protected $updateRules;

    protected $orderBy = ['id', 'desc'];

    protected $with = [];

    protected $listFormat = 'laravel';

    protected $indexFormat;

    protected $indexAllFormat;

    protected $searchFormat;

    protected $perPage = 10;

    protected $indexPerPage;

    protected $indexAllPerPage;

    protected $useRequestAll = false;

    protected $fileFields = [];

    protected $dateFields = [];

    protected $syncDetails = false;

    protected $detailTable;

    protected $detailForeignKey;

    protected $authExcept = ['indexAll'];

    protected $listMessage;

    protected $indexAllMessage;

    protected $writeResponse = 'standard';

    protected $destroyResponse;

    protected $showResponse = 'standard';

    protected $legacyMessages = [];

    protected $legacyStoreIdKey;

    protected $writeErrorStatus = 422;

    protected $legacyValidationStatus = 200;

    protected $listMode;

    private static $detailColumns = [];

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => $this->authExcept]);

        if ($this->resourcePlural === null) {
            $this->resourcePlural = $this->resource;
        }
    }

    protected function normalizeDates(array $data)
    {
        if (empty($this->dateFields)) {
            return $data;
        }

        foreach ($this->dateFields as $field => $format) {
            // Support both ['closingDate'] and ['closingDate' => 'Y-m-d']
            if (is_int($field)) {
                $field = $format;
                $format = 'Y-m-d H:i:s';
            }

            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                continue;
            }

            try {
                $data[$field] = Carbon::parse($data[$field])->format($format);
            } catch (\Throwable $e) {
                // Leave original value; validation will catch invalid dates
            }
        }

        return $data;
    }

    public function indexAll(Request $request)
    {
        return $this->listResponse($request, 'indexAll');
    }

    public function index(Request $request)
    {
        return $this->listResponse($request, 'index');
    }

    public function search(Request $request)
    {
        return $this->listResponse($request, 'search');
    }

    public function show($id)
    {
        $this->listMode = null;

        try {
            $item = $this->findItem($id);

            if ($item === null) {
                return $this->notFound();
            }

            return $this->respondShow($item);
        } catch (ModelNotFoundException $e) {
            return $this->notFound();
        } catch (\Throwable $e) {
            return ResponseRepository::ResponseError(null, $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        $this->listMode = null;

        try {
            $this->validateRequest($request, 'store');

            $data = $this->beforeStore($request, $this->payload($request));
            $data = $this->normalizeDates($data);

            if ($this->isDbMode()) {
                $data = $this->withTimestamps($data, true);
                $id = $this->query()->insertGetId($data);
                $item = $this->query()->where('id', $id)->first();
            } else {
                $model = $this->model;
                $item = $model::create($data);
                $id = $item->getKey();
            }

            $this->afterStore($request, $item);
            $this->syncDetails($request, $item);

            $extra = $this->legacyStoreIdKey ? [$this->legacyStoreIdKey => $id] : [];

            return $this->respondWrite($item, $this->message('store'), 201, $extra);
        } catch (ValidationException $e) {
            $status = $this->writeResponse === 'legacy' ? $this->legacyValidationStatus : 422;

            return $this->respondWriteError(
                $e->validator->errors()->first(),
                $status,
                $e->errors()
            );
        } catch (\Throwable $e) {
            return $this->respondWriteError($e->getMessage(), $this->writeErrorStatus);
        }
    }

    public function update(Request $request, $id = null)
    {
        $this->listMode = null;

        try {
            $id = $id ?: $request->input('id');
            $item = $this->findItem($id);

            if ($item === null) {
                return $this->notFoundForWrite();
            }

            $this->validateRequest($request, 'update');

            $data = $this->beforeUpdate($request, $this->payload($request, $item), $item);
            $data = $this->normalizeDates($data);

            if ($this->isDbMode()) {
                $data = $this->withTimestamps($data, false);
                $this->query()->where('id', $id)->update($data);
                $item = $this->query()->where('id', $id)->first();
            } else {
                $item->update($data);
            }

            $this->afterUpdate($request, $item);
            $this->syncDetails($request, $item);

            return $this->respondWrite($item, $this->message('update'));
        } catch (ModelNotFoundException $e) {
            return $this->notFoundForWrite();
        } catch (ValidationException $e) {
            $status = $this->writeResponse === 'legacy' ? $this->legacyValidationStatus : 422;

            return $this->respondWriteError(
                $e->validator->errors()->first(),
                $status,
                $e->errors()
            );
        } catch (\Throwable $e) {
            return $this->respondWriteError($e->getMessage(), $this->writeErrorStatus);
        }
    }

    public function destroy($id)
    {
        $this->listMode = null;
        $mode = $this->destroyResponse ?: $this->writeResponse;

        try {
            $item = $this->findItem($id);

            if ($item === null) {
                return $this->notFoundForWrite($mode);
            }

            $this->beforeDestroy($item);

            if ($this->isDbMode()) {
                $this->query()->where('id', $id)->delete();
            } else {
                $item->delete();
            }

            $this->afterDestroy($item);

            return $this->respondWrite(null, $this->message('destroy'), 200, [], $mode);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundForWrite($mode);
        } catch (\Throwable $e) {
            return $this->respondWriteError($e->getMessage(), 500, null, $mode);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', array_values($request->all()));

            if (!is_array($ids)) {
                $ids = array_filter(explode(',', (string) $ids));
            }

            if ($this->isDbMode()) {
                $this->query()->whereIn('id', $ids)->delete();
            } else {
                $model = $this->model;
                $model::whereIn('id', $ids)->delete();
            }

            return $this->respondWrite(null, $this->message('bulk'));
        } catch (\Throwable $e) {
            return $this->respondWriteError($e->getMessage(), 500);
        }
    }

    protected function listResponse(Request $request, $mode = 'index')
    {
        try {
            $this->listMode = $mode;
            $perPage = $this->resolvePerPage($request, $mode);

            $query = $this->query();

            if ($mode === 'search' || $this->searchOnList) {
                $this->applySearch($query, (string) $request->input('search', ''));
            }

            $this->applyFilters($query, $request);
            $this->applyListQuery($query, $request);

            $items = $query->orderBy($this->orderBy[0], $this->orderBy[1])->paginate($perPage);

            return ResponseRepository::ResponseSuccess(
                $this->formatList($items, $request, $perPage, $this->formatFor($mode)),
                $this->message($mode)
            );
        } catch (\Throwable $e) {
            return ResponseRepository::ResponseError(null, $e->getMessage(), 500);
        }
    }

    protected function resolvePerPage(Request $request, $mode)
    {
        if ($mode === 'index' && $this->indexPerPage !== null) {
            return (int) $this->indexPerPage;
        }

        if ($mode === 'indexAll' && $this->indexAllPerPage !== null) {
            return (int) $this->indexAllPerPage;
        }

        return (int) $request->input(
            'pageSize',
            $request->input('per_page', $request->input('perPage', $this->perPage))
        );
    }

    protected function formatFor($mode)
    {
        if ($mode === 'index' && $this->indexFormat !== null) {
            return $this->indexFormat;
        }

        if ($mode === 'indexAll' && $this->indexAllFormat !== null) {
            return $this->indexAllFormat;
        }

        if ($mode === 'search' && $this->searchFormat !== null) {
            return $this->searchFormat;
        }

        return $this->listFormat;
    }

    protected function isDbMode()
    {
        return $this->dbTable !== null;
    }

    protected function query()
    {
        if ($this->isDbMode()) {
            return DB::table($this->dbTable);
        }

        $model = $this->model;
        $query = $model::query();

        if (!empty($this->with)) {
            $query->with($this->with);
        }

        return $query;
    }

    protected function findItem($id)
    {
        if ($this->isDbMode()) {
            return $this->query()->where('id', $id)->first();
        }

        return $this->query()->find($id);
    }

    protected function modelInstance()
    {
        $model = $this->model;

        return new $model;
    }

    protected function applySearch($query, $search)
    {
        if ($search === '' || empty($this->searchable)) {
            return;
        }

        $query->where(function ($q) use ($search) {
            foreach ($this->searchable as $column) {
                $q->orWhere($column, 'LIKE', '%' . $search . '%');
            }
        });
    }

    protected function applyFilters($query, Request $request)
    {
        foreach ($this->filters as $key => $column) {
            $value = $request->input($key);

            if ($value !== null && $value !== '') {
                $query->where($column, $value);
            }
        }
    }

    protected function applyListQuery($query, Request $request)
    {
    }

    protected function formatList($items, Request $request, $perPage, $format = null)
    {
        $format = $format ?: $this->listFormat;

        if ($format === 'compact') {
            return [
                'pageIndex' => $items->currentPage(),
                'pageSize' => (int) $perPage,
                'count' => $items->total(),
                'data' => $items->items(),
            ];
        }

        if ($format === 'items') {
            return $items->items();
        }

        $data = [
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
        ];

        if ($format === 'paginated_links') {
            $data['links'] = $this->paginationLinks($items, $request);
        }

        return $data;
    }

    protected function paginationLinks($items, Request $request)
    {
        $links = [];
        $baseUrl = $request->url();
        $currentPage = $items->currentPage();
        $perPage = $items->perPage();

        for ($i = 1; $i <= $items->lastPage(); $i++) {
            $links[] = [
                'url' => $i === $currentPage ? null : $baseUrl . '?page=' . $i . '&per_page=' . $perPage,
                'label' => $i,
                'active' => $i === $currentPage,
            ];
        }

        return $links;
    }

    protected function validateRequest(Request $request, $action)
    {
        $rules = $action === 'update' && $this->updateRules !== null
            ? $this->updateRules
            : $this->rules;

        if (!empty($rules)) {
            $request->validate($rules);
        }
    }

    protected function payload(Request $request, $existing = null)
    {
        if ($this->useRequestAll) {
            $data = $request->all();
            unset($data['id']);
        } else {
            $fields = $this->isDbMode() ? $this->fields : $this->modelInstance()->getFillable();
            $data = [];

            foreach ($fields as $field) {
                $found = false;
                $value = null;

                foreach (array_unique([$field, Str::camel($field), Str::snake($field)]) as $key) {
                    if ($request->exists($key)) {
                        $found = true;
                        $candidate = $request->input($key);

                        if ($candidate !== null) {
                            $value = $candidate;
                            break;
                        }
                    }
                }

                if ($found) {
                    $data[$field] = $value;
                }
            }

            if ($this->isDbMode() && ($existing === null || $this->clearMissingOnUpdate)) {
                foreach ($fields as $field) {
                    if (!array_key_exists($field, $data)) {
                        $data[$field] = null;
                    }
                }
            }
        }

        foreach ($this->fileFields as $field => $directory) {
            if (isset($data[$field]) && $data[$field] instanceof UploadedFile) {
                $data[$field] = $data[$field]->store($directory, 'public');
            }
        }

        return $data;
    }

    protected function withTimestamps(array $data, $creating)
    {
        if (!$this->timestamps) {
            return $data;
        }

        $now = now();

        if ($creating) {
            $data['created_at'] = $data['created_at'] ?? $now;
        }

        $data['updated_at'] = $data['updated_at'] ?? $now;

        return $data;
    }

    protected function respondWrite($item, $message, $status = 200, array $extra = [], $mode = null)
    {
        $mode = $mode ?: $this->writeResponse;

        if ($mode === 'legacy') {
            return response()->json(array_merge([
                'message' => $message,
                'type' => 'success',
            ], $extra));
        }

        return ResponseRepository::ResponseSuccess($item, $message, $status);
    }

    protected function respondWriteError($message, $status = 422, $errors = null, $mode = null)
    {
        $mode = $mode ?: $this->writeResponse;

        if ($mode === 'legacy') {
            if ($status === 422) {
                $status = 500;
            }

            return response()->json(['message' => $message, 'type' => 'error'], $status);
        }

        return ResponseRepository::ResponseError($errors, $message, $status);
    }

    protected function respondShow($item)
    {
        if ($this->showResponse === 'legacy') {
            return response()->json([
                'data' => $item,
                'message' => $this->message('show'),
                'type' => 'success',
            ]);
        }

        return ResponseRepository::ResponseSuccess($item, $this->message('show'));
    }

    protected function beforeStore(Request $request, array $data)
    {
        return $data;
    }

    protected function afterStore(Request $request, $item)
    {
    }

    protected function beforeUpdate(Request $request, array $data, $item)
    {
        return $data;
    }

    protected function afterUpdate(Request $request, $item)
    {
    }

    protected function beforeDestroy($item)
    {
    }

    protected function afterDestroy($item)
    {
    }

    protected function syncDetails(Request $request, $item)
    {
        if (!$this->syncDetails || !$request->has('details')) {
            return;
        }

        $details = $request->input('details');

        if (!is_array($details) || empty($details)) {
            return;
        }

        $table = $this->detailTableFor($item);
        $foreignKey = $this->detailForeignKeyFor($item);

        DB::table($table)->where($foreignKey, $this->itemKey($item))->delete();
        $this->insertDetails($item, $details);
    }

    protected function itemKey($item)
    {
        if (is_object($item) && method_exists($item, 'getKey')) {
            return $item->getKey();
        }

        return is_object($item) ? ($item->id ?? null) : null;
    }

    protected function detailTableFor($item)
    {
        if ($this->detailTable) {
            return $this->detailTable;
        }

        if (is_object($item) && method_exists($item, 'getTable')) {
            return Str::singular($item->getTable()) . '_details';
        }

        return Str::singular($this->dbTable) . '_details';
    }

    protected function detailForeignKeyFor($item)
    {
        if ($this->detailForeignKey) {
            return $this->detailForeignKey;
        }

        if (is_object($item) && method_exists($item, 'getForeignKey')) {
            return $item->getForeignKey();
        }

        return Str::camel(Str::singular($this->dbTable)) . 'Id';
    }

    protected function insertDetails($item, array $details)
    {
        $table = $this->detailTableFor($item);
        $foreignKey = $this->detailForeignKeyFor($item);

        if (!isset(self::$detailColumns[$table])) {
            self::$detailColumns[$table] = Schema::getColumnListing($table);
        }

        $columns = array_flip(self::$detailColumns[$table]);
        $now = now();

        $itemKey = $this->itemKey($item);

        foreach ($details as $detail) {
            if (!is_array($detail)) {
                continue;
            }

            $detail[$foreignKey] = $itemKey;

            if (isset($columns['created_at'])) {
                $detail['created_at'] = $detail['created_at'] ?? $now;
            }

            if (isset($columns['updated_at'])) {
                $detail['updated_at'] = $detail['updated_at'] ?? $now;
            }

            $detail = array_filter($detail, static fn ($value) => $value !== null);
            $detail = array_intersect_key($detail, $columns);

            if (!empty($detail)) {
                DB::table($table)->insert($detail);
            }
        }
    }

    protected function message($action)
    {
        if (isset($this->legacyMessages[$action])) {
            return $this->legacyMessages[$action];
        }

        switch ($action) {
            case 'show':
                return $this->resource . ' fetched successfully!';
            case 'store':
                return $this->resource . ' created successfully!';
            case 'update':
                return $this->resource . ' updated successfully!';
            case 'destroy':
                return $this->resource . ' deleted successfully!';
            case 'bulk':
                return $this->resourcePlural . ' deleted successfully!';
            case 'search':
                return $this->resource . ' search results fetched successfully!';
            case 'indexAll':
                return $this->indexAllMessage ?: ($this->listMessage ?: $this->resourcePlural . ' fetched successfully!');
            case 'not_found':
                return $this->resource . ' not found';
            default:
                return $this->listMessage ?: $this->resourcePlural . ' fetched successfully!';
        }
    }

    protected function notFound()
    {
        if ($this->showResponse === 'legacy') {
            return response()->json([
                'message' => $this->message('not_found'),
                'type' => 'error',
            ], 404);
        }

        return ResponseRepository::ResponseError(null, $this->message('not_found'), 404);
    }

    protected function notFoundForWrite($mode = null)
    {
        return $this->respondWriteError($this->message('not_found'), 404, null, $mode);
    }
}
