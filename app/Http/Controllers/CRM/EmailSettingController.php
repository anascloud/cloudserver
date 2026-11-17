<?php

namespace App\Http\Controllers\CRM;

class EmailSettingController extends CrmCrudController
{
    protected $dbTable = 'emailsettings';

    protected $resource = 'Email Setting';

    protected $resourcePlural = 'Email Settings';

    // Email settings are edited field-by-field; never blank out columns that
    // were not part of the request payload.
    protected $clearMissingOnUpdate = false;

    protected $fields = [
        'protocol',
        'encryption',
        'email',
        'charset',
        'smtpHost',
        'smtpPort',
        'smtpUsername',
        'smtpPassword',
        'signature',
    ];

    protected $searchable = ['email', 'smtpHost', 'smtpUsername'];

    protected $searchOnList = false;

    protected $rules = [
        'protocol' => 'nullable|integer|min:0',
        'encryption' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'charset' => 'nullable|string|max:255',
        'smtpHost' => 'nullable|string|max:255',
        'smtpPort' => 'nullable|integer|min:1|max:65535',
        'smtpUsername' => 'nullable|string|max:255',
        'smtpPassword' => 'nullable|string|max:255',
        'signature' => 'nullable|string',
    ];

    protected $listMessage = 'Email Settings Fetched Successfully!';

    protected $indexAllMessage = 'All Email Settings Fetched Successfully!';

    protected $legacyMessages = [
        'show' => 'Email Setting Fetched Successfully!',
        'store' => 'Email Setting Created Successfully!',
        'update' => 'Email Setting Updated Successfully!',
        'destroy' => 'Email Setting Deleted Successfully!',
        'bulk' => 'Email Settings Deleted Successfully!',
        'search' => 'Email Setting Search Results Fetched Successfully!',
        'not_found' => 'Email Setting Not Found',
    ];
}