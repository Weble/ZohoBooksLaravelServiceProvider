<?php
return [
    /**
     * The Zoho Books API AuthToken
     * @see https://accounts.zoho.com/apiauthtoken/create?SCOPE=ZohoBooks%2Fbooksapi
     */
    'authtoken' => env('ZOHOBOOKS_AUTHTOKEN', ''),

    /**
     * The organization ID you want to work on.
     * If the account you provided has just one organization, this can be null
     */
    'organization_id' => env('ZOHOBOOKS_ORGANIZATION_ID', null),

    /**
     * The region in which your account is under. Use "US" for books.zoho.com
     * or "EU" for books.zoho.eu
     */
    'region' => 'US', // "US" or "EU"

    /**
     * When you need the zoho books invoice PDF, it gets stored locally for cache.
     * This is the path in the storage dir where it gets stored as a pdf file
     */
    'invoice_storage_path' => 'invoices'
];