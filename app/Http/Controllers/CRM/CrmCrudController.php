<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\ApiCrudController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

abstract class CrmCrudController extends ApiCrudController
{
    protected $clearMissingOnUpdate = true;

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $createdAtKey = 'created_at';

    protected function normalizeKey($key)
    {
        return strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $key));
    }

    protected function payload(Request $request, $existing = null)
    {
        if ($this->useRequestAll || !$this->isDbMode()) {
            return parent::payload($request, $existing);
        }

        $requestKeys = [];

        foreach ($request->keys() as $key) {
            $requestKeys[$this->normalizeKey($key)] = $key;
        }

        $data = [];

        foreach ($this->fields as $field) {
            $found = false;
            $value = null;

            $candidates = array_unique([$field, Str::camel($field), Str::snake($field)]);

            foreach ($candidates as $candidate) {
                $requestKey = $requestKeys[$this->normalizeKey($candidate)] ?? null;

                if ($requestKey === null || !$request->exists($requestKey)) {
                    continue;
                }

                $found = true;
                $candidateValue = $request->input($requestKey);

                if ($candidateValue !== null) {
                    $value = $candidateValue;
                    break;
                }
            }

            if ($found) {
                $data[$field] = $value;
            }
        }

        foreach ($this->fileFields as $field => $directory) {
            $requestKey = $requestKeys[$this->normalizeKey($field)] ?? null;

            if ($requestKey !== null && $request->hasFile($requestKey)) {
                $data[$field] = $request->file($requestKey)->store($directory, 'public');
                continue;
            }

            if (isset($data[$field]) && $data[$field] instanceof UploadedFile) {
                $data[$field] = $data[$field]->store($directory, 'public');
            }
        }

        if ($existing === null || $this->clearMissingOnUpdate) {
            foreach ($this->fields as $field) {
                if (!array_key_exists($field, $data)) {
                    $data[$field] = null;
                }
            }
        }

        return $data;
    }

    protected function booleanValue($value)
    {
        if (is_string($value)) {
            $normalized = strtolower($value);

            if (in_array($normalized, ['true', '1', 'yes', 'on'], true)) {
                return 1;
            }

            if (in_array($normalized, ['false', '0', 'no', 'off', ''], true)) {
                return 0;
            }
        }

        return $value ? 1 : 0;
    }

    protected function formatList($items, Request $request, $perPage, $format = null)
    {
        $data = parent::formatList($items, $request, $perPage, $format);

        if (isset($data['data']) && is_array($data['data'])) {
            foreach ($data['data'] as $row) {
                $this->decorateRow($row);
            }
        }

        return $data;
    }

    protected function decorateRow($row)
    {
        if (is_object($row)) {
            if (!isset($row->createdAt) && isset($row->{$this->createdAtKey})) {
                $row->createdAt = $row->{$this->createdAtKey};
            }

            if (!isset($row->shortOrder)) {
                $row->shortOrder = $row->id ?? null;
            }
        }

        return $row;
    }
}
