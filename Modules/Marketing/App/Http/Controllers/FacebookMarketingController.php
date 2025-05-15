<?php

namespace Modules\Marketing\App\Http\Controllers;

use FacebookAds\Api;
use Inertia\Inertia;
use Illuminate\Http\Request;
use FacebookAds\Object\AdAccount;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Marketing\App\Services\FacebookService;


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
        $this->initFacebookApi();
        $account = new AdAccount('me');
        $accounts = $account->getAdAccounts();
        dd($accounts);
        // return Inertia::module('marketing/facebook/Index');
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
