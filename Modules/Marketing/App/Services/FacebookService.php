<?php
namespace Modules\Marketing\App\Services;
use App\Models\User;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use Illuminate\Support\Facades\Log;
use FacebookAds\Exception\ApiException;



class FacebookService
{
    public $user;
    public $facebook_app_id;
    public $facebook_app_secret;
    public $facebook_app_access_token;
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->facebook_app_access_token = $user->tokens->facebook_token;
        $this->facebook_app_id = config('services.facebook.client_id');
        $this->facebook_app_secret = config('services.facebook.client_secret');
        Log::info('Facebook API initialized for user: ');

        $this->initFacebookApi();
    }
    public function initFacebookApi()
    {
        Api::init($this->facebook_app_id, $this->facebook_app_secret, $this->facebook_app_access_token);
        $api = Api::instance();
    }

    public function getAdAccounts()
    {
        try {
            $account = new AdAccount('me');
            $accounts = $account->getAdAccounts();
            if ($accounts->isEmpty()) {
                Log::info('No Facebook Ad Accounts found for user: ' . $this->user->id);
                return null; // Or throw an exception
            }
            return $accounts;
        } catch (ApiException $e) {
            Log::error('Error fetching Facebook Ad Accounts: ' . $e->getMessage());
            return null; // Or throw an exception
        }
    }
}