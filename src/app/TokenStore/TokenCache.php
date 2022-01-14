<?php
// Copyright (c) Microsoft Corporation.
// Licensed under the MIT License.
namespace App\TokenStore;

use App\Constant_value;
use Illuminate\Support\Facades\Cache;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Microsoft\Graph\Graph;

class TokenCache
{
    public function getGraph(): Graph
    {
        // Get the　委員 access token from the cache
        $accessToken = $this->getAccessTokenFromRefreshToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);
        return $graph;
    }

    public function storeTokens($accessToken)
    {
        // Cache::forever('refreshToken', $accessToken->getRefreshToken());//fixme
        $refreshToken = Constant_value::where('name','refresh_token')->first();
        $refreshToken->value = $accessToken->getRefreshToken();
        $refreshToken->update();
        session([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'tokenExpires' => $accessToken->getExpires(),
        ]);
    }

    public function clearTokens()
    {
        session()->forget('accessToken');
        session()->forget('refreshToken');
        session()->forget('tokenExpires');
    }


    public function getAccessTokenFromRefreshToken(){
        // $refreshToken = Cache::get('refreshToken');//fixme
        $refreshToken = Constant_value::where('name','refresh_token')->first()->value;
        //　APIのconfig 　委員のアクセストークンを取得できるように認証を受けるための設定
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => config('azure.appId'),
            'clientSecret'            => config('azure.appSecret'),
            'redirectUri'             => config('azure.redirectUri'),
            'urlAuthorize'            => config('azure.authority') . config('azure.authorizeEndpoint'),
            'urlAccessToken'          => config('azure.authority') . config('azure.tokenEndpoint'),
            'urlResourceOwnerDetails' => '',
            'scopes'                  => config('azure.scopes')
        ]);
        try {
            //　アクセストークンAPI
            $newToken = $oauthClient->getAccessToken('refresh_token', [
                'refresh_token' => $refreshToken
            ]);
            // Store the new values
            $this->updateTokens($newToken);

            return $newToken;
        } catch (IdentityProviderException $e) {
            return '';
        }
    }

    public function getAccessToken()
    {
        // Check if tokens exist
        if (
            empty(session('accessToken')) ||
            empty(session('refreshToken')) ||
            empty(session('tokenExpires'))
        ) {
            return '';
        }

        // Check if token is expired
        //Get current time + 5 minutes (to allow for time differences)
        $now = time() + 300;
        if (session('tokenExpires') <= $now) {
            // Token is expired (or very close to it)
            // so let's refresh

            // Initialize the OAuth client
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId'                => env('OAUTH_APP_ID'),
                'clientSecret'            => env('OAUTH_APP_PASSWORD'),
                'redirectUri'             => env('OAUTH_REDIRECT_URI'),
                'urlAuthorize'            => env('OAUTH_AUTHORITY') . env('OAUTH_AUTHORIZE_ENDPOINT'),
                'urlAccessToken'          => env('OAUTH_AUTHORITY') . env('OAUTH_TOKEN_ENDPOINT'),
                'urlResourceOwnerDetails' => '',
                'scopes'                  => env('OAUTH_SCOPES')
            ]);
            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => session('refreshToken')
                ]);

                // Store the new values
                $this->updateTokens($newToken);

                return $newToken->getToken();
            } catch (IdentityProviderException $e) {
                return '';
            }
        }

        // Token is still valid, just return it
        return session('accessToken');
    }

    // public function getAccessToken()
    // {
    //     // Check if tokens exist
    //     if (
    //         empty(session('accessToken')) ||
    //         empty(session('refreshToken')) ||
    //         empty(session('tokenExpires'))
    //     ) {
    //         return '';
    //     }
    //     return session('accessToken');
    // }

    // <UpdateTokensSnippet>
    public function updateTokens($accessToken)
    {
        if($accessToken->getToken() != Cache::get('refreshToken')){
            Cache::forever('refreshToken', $accessToken->getRefreshToken());
        }
        session([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'tokenExpires' => $accessToken->getExpires()
        ]);
    }
    // </UpdateTokensSnippet>
}
