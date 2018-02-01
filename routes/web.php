<?php

Route::group(['middleware' => ['web']], function ($router) {
    $router->get('/webhook/stripe', 'Webleit\ZohoBooksLaravelServiceProvider\Http\Controllers\StripeWebhookController@handleWebhook');
});