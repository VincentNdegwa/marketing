<?php

namespace Modules\Marketing\App\Http\Controllers;

use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Fields\AdAccountFields;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Marketing\App\Services\FacebookService;
use GuzzleHttp\Client;


class FacebookMarketingController extends Controller
{
    public $facebook_app_id;
    public $facebook_app_secret;
    public $facebook_app_access_token;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            Log::error('No authenticated user found.');
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }
    
        $accessToken = $user->tokens->facebook_token;
        if (!$accessToken) {
            return back()->with('error', 'No Facebook access token found. Please connect your Facebook account first.');
        }
    
        try {
            // First, verify the access token
            $client = new Client();
            $debugUrl = 'https://graph.facebook.com/v18.0/debug_token';
            $debugResponse = $client->get($debugUrl, [
                'query' => [
                    'input_token' => $accessToken,
                    'access_token' => config('services.facebook.client_id') . '|' . config('services.facebook.client_secret')
                ]
            ]);
            
            $debugData = json_decode($debugResponse->getBody(), true);
            Log::info('Facebook token debug info', $debugData);
    
            // Check if the token has ads_management permission
            if (!isset($debugData['data']['scopes']) || !in_array('ads_management', $debugData['data']['scopes'])) {
                return back()->with('error', 'Facebook access token doesn\'t have ads_management permission. Please reconnect your Facebook account.');
            }
    
            // Now try to get ad accounts
            $url = 'https://graph.facebook.com/v18.0/me/adaccounts';
            $fields = 'id,name,account_status,currency,timezone_name';
    
            $response = $client->get($url, [
                'query' => [
                    'fields' => $fields,
                    'access_token' => $accessToken
                ]
            ]);
    
            $accounts = json_decode($response->getBody(), true)['data'] ?? [];
    
            // Format the accounts data for Inertia
            $formattedAccounts = collect($accounts)->map(function ($account) {
                return [
                    'id' => $account['id'],
                    'name' => $account['name'],
                    'status' => $account['account_status'],
                    'currency' => $account['currency'],
                    'timezone' => $account['timezone_name']
                ];
            });
    
            Log::info("Fetched Facebook ad accounts successfully", [
                'user_id' => $user->id,
                'accounts' => $accounts
            ]);
    
            return Inertia::module('marketing/facebook/Index', [
                'accounts' => $formattedAccounts
            ]);
    
        } catch (\Exception $e) {
            Log::error('Failed to fetch Facebook ad accounts: ' . $e->getMessage(), [
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to fetch Facebook ad accounts: ' . $e->getMessage());
        }
    }

    public function initFacebookApi()
    {
        $user = Auth::user();
        $this->facebook_app_access_token = $user->tokens->facebook_token;
        $this->facebook_app_id = config('services.facebook.client_id');
        $this->facebook_app_secret = config('services.facebook.client_secret');

        Api::init($this->facebook_app_id, $this->facebook_app_secret, $this->facebook_app_access_token);
        $api = Api::instance();
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::module('marketing/facebook/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return Inertia::module('marketing/facebook/Show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return Inertia::module('marketing/facebook/Edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
