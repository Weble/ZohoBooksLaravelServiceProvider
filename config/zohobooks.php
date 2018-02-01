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
    'organization_id' => env('ZOHOBOOKS_ORGANIZATION_ID', null)
];