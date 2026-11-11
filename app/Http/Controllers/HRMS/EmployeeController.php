<?php

namespace App\Http\Controllers\HRMS;

use App\Http\Controllers\ApiCrudController;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends ApiCrudController
{
    protected $dbTable = 'hr_employees';

    /**
     * Bind the Eloquent model so the base controller can use relationships,
     * casts, timestamps and route-model binding like the Salesorder controller.
     */
    protected $model = Employee::class;

    protected $resource = 'Employee';

    protected $resourcePlural = 'Employees';

    protected $fields = [
        'employeeName',
        'employeeManagerId',
        'company_id',
        'company_name',
        'location',
        'capacity',
        'inUseCapacity',
        'emergencyContact',
        'binLocation',
        'zoningLocation',
        'startHour',
        'endHour',
        'pickedBy',
        'packedBy',
        'quantityToPick',
        'datePicked',
        'temperatureControlled',
        'fireSafetyCompliance',
        'status',
    ];

    protected $dateFields = [
        'datePicked' => 'Y-m-d H:i:s',
    ];

    protected $searchable = ['employeeName', 'location', 'binLocation', 'zoningLocation'];

    protected $searchOnList = false;

    protected $clearMissingOnUpdate = true;

    protected $rules = [
        'employeeName'         => 'required|string|max:255',
        'employeeManagerId'    => 'required|integer|exists:users,id',
        'company_id'           => 'required|integer|exists:fm_companies,id',
        'company_name'         => 'required|string|max:255',
        'location'             => 'required|string|max:500',
        'capacity'             => 'required|integer|min:0',
        'inUseCapacity'        => 'required|integer|min:0',
        'emergencyContact'     => 'nullable|string|max:100',
        'binLocation'          => 'nullable|string|max:255',
        'zoningLocation'       => 'nullable|string|max:255',
        'startHour'            => 'required|date_format:H:i:s',
        'endHour'              => 'required|date_format:H:i:s|after:startHour',
        'pickedBy'             => 'required|string|max:255',
        'packedBy'             => 'required|string|max:255',
        'quantityToPick'       => 'required|integer|min:0',
        'datePicked'           => 'required|date',
        'temperatureControlled'=> 'required|boolean',
        'fireSafetyCompliance' => 'required|boolean',
        'status'               => 'nullable|boolean',
    ];

    protected $indexFormat = 'paginated_links';

    protected $indexAllFormat = 'items';

    protected $searchFormat = 'items';

    protected $writeResponse = 'legacy';

    protected $showResponse = 'legacy';

    protected $legacyStoreIdKey = 'employeeId';

    protected $listMessage = 'employee List Fetched Successfully!';

    protected $indexAllMessage = 'All employees Fetched Successfully!';

    protected $legacyMessages = [
        'show'      => 'employee Details Fetched Successfully!',
        'store'     => 'New employee Created Successfully!',
        'update'    => 'employee Updated Successfully!',
        'destroy'   => 'employee Deleted Successfully!',
        'search'    => 'employee Search Results Fetched Successfully!',
        'not_found' => 'employee Not Found',
    ];

    /**
     * Normalize the string booleans the multipart form sends
     * ("true"/"false", "1"/"0") into real booleans so the
     * `boolean` rule accepts them.
     */
    protected function validateRequest(Request $request, $action)
    {
        foreach (['temperatureControlled', 'fireSafetyCompliance', 'status'] as $boolField) {
            if ($request->has($boolField)) {
                $raw = $request->input($boolField);

                if (is_string($raw) && in_array(strtolower($raw), ['true', 'false'], true)) {
                    $request->merge([$boolField => strtolower($raw) === 'true']);
                }
            }
        }

        parent::validateRequest($request, $action);
    }

    /**
     * Keep the legacy response body shape but never rewrite a 422 into a 500.
     */
    protected function respondWriteError($message, $status = 422, $errors = null, $mode = null)
    {
        if (($mode ?: $this->writeResponse) === 'legacy') {
            return response()->json(['message' => $message, 'type' => 'error'], $status);
        }

        return parent::respondWriteError($message, $status, $errors, $mode);
    }

    protected function beforeStore(Request $request, array $data)
    {
        $data = parent::beforeStore($request, $data);

        // Sensible defaults for a freshly created employee.
        if (!isset($data['status'])) {
            $data['status'] = 1;
        }
        if (!isset($data['inUseCapacity'])) {
            $data['inUseCapacity'] = 0;
        }
        if (array_key_exists('temperatureControlled', $data)) {
            $data['temperatureControlled'] = filter_var($data['temperatureControlled'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }
        if (array_key_exists('fireSafetyCompliance', $data)) {
            $data['fireSafetyCompliance'] = filter_var($data['fireSafetyCompliance'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }
        if (array_key_exists('status', $data)) {
            $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        return $data;
    }

    protected function beforeUpdate(Request $request, array $data, $item)
    {
        $data = parent::beforeUpdate($request, $data, $item);

        foreach (['temperatureControlled', 'fireSafetyCompliance', 'status'] as $boolField) {
            if (array_key_exists($boolField, $data)) {
                $data[$boolField] = filter_var($data[$boolField], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
        }

        return $data;
    }

    protected function applyListQuery($query, Request $request)
    {
        if ($this->listMode === 'index') {
            $query->select(
                'id',
                'employeeName',
                'employeeManagerId',
                'company_id',
                'company_name',
                'location',
                'capacity',
                'inUseCapacity',
                'emergencyContact',
                'binLocation',
                'zoningLocation',
                'startHour',
                'endHour',
                'pickedBy',
                'packedBy',
                'quantityToPick',
                'datePicked',
                'temperatureControlled',
                'fireSafetyCompliance',
                'status',
                'created_at',
                'updated_at'
            );
        }
    }
}
