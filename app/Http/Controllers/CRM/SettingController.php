<?php

namespace App\Http\Controllers\CRM;

class SettingController extends CrmCrudController
{
    protected $dbTable = 'generalsettings';

    protected $resource = 'General Setting';

    protected $resourcePlural = 'General Settings';

    // General settings are edited field-by-field; never blank out columns that
    // were not part of the request payload.
    protected $clearMissingOnUpdate = false;

    protected $fields = [
        'appcompany_name',
        'defaultEmail',
        'mobile',
        'currency',
        'street',
        'house',
        'zip',
        'city',
        'state',
        'defaultInvoiceTerms',
    ];

    protected $searchable = ['appcompany_name', 'defaultEmail', 'city', 'state'];

    protected $searchOnList = false;

    protected $rules = [
        'appcompany_name' => 'nullable|string|max:255',
        'defaultEmail' => 'nullable|email|max:255',
        'mobile' => 'nullable|string|max:50',
        'currency' => 'nullable',
        'street' => 'nullable|string|max:255',
        'house' => 'nullable|string|max:255',
        'zip' => 'nullable|string|max:50',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'defaultInvoiceTerms' => 'nullable|string',
    ];

    protected $listMessage = 'General Settings Fetched Successfully!';

    protected $indexAllMessage = 'All General Settings Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'General Setting Fetched Successfully!',
        'store' => 'General Setting Created Successfully!',
        'update' => 'General Setting Updated Successfully!',
        'destroy' => 'General Setting Deleted Successfully!',
        'bulk' => 'General Settings Deleted Successfully!',
        'search' => 'General Setting Search Results Fetched Successfully!',
        'not_found' => 'General Setting Not Found',
    ];
}
