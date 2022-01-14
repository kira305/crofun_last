<?php
// Copyright (c) Microsoft Corporation.
// Licensed under the MIT License.

// Access environment through the config helper
// This will avoid issues when using Laravel's config caching
// https://laravel.com/docs/8.x/configuration#configuration-caching
return [
    'appId'             => env('OAUTH_APP_ID', '6f9303d3-0b5b-43a0-80f6-0a7ee5a06c1b'),
    'appSecret'         => env('OAUTH_APP_SECRET', 'TeK6k9E34QS4Z.5JC.0_YFPf.~WIxj2RYh'),
    'redirectUri'       => env('OAUTH_REDIRECT_URI', 'http://localhost:8000/callback'),
    'scopes'            => env('OAUTH_SCOPES', 'openid profile offline_access user.read chatMessage.Send chat.ReadWrite Chat.Read Chat.ReadBasic'),
    'authority'         => env('OAUTH_AUTHORITY', 'https://login.microsoftonline.com/common'),
    'authorizeEndpoint' => env('OAUTH_AUTHORIZE_ENDPOINT', '/oauth2/v2.0/authorize'),
    'tokenEndpoint'     => env('OAUTH_TOKEN_ENDPOINT', '/oauth2/v2.0/token'),
    'tokenAppEndpoint'  => env('OAUTH_TOKEN_APP_ENDPOINT', 'https://login.microsoftonline.com/2dc8cdf2-eb35-41a0-939d-51afa1c7cd76/oauth2/token?api-version=1.0'),
];
