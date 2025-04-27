<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Business\app\Models\Business;

if (!function_exists('getCurrentBusiness')) {
    function getCurrentBusiness()
    {
        $currentBusinessId = Session::get('current_business_id');
        
        if ($currentBusinessId) {
            return Business::find($currentBusinessId);
        }
        
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        
        $defaultBusiness = $user->defaultBusiness();
        if ($defaultBusiness) {
            setCurrentBusiness($defaultBusiness->id);
            return $defaultBusiness;
        }
        
        $firstBusiness = $user->businesses()->first();
        if ($firstBusiness) {
            setCurrentBusiness($firstBusiness->id);
            return $firstBusiness;
        }        
        return null;
    }
}

if (!function_exists('getCurrentBusinessId')) {
    function getCurrentBusinessId()
    {
        $business = getCurrentBusiness();
        return $business ? $business->id : null;
    }
}

if (!function_exists('setCurrentBusiness')) {
    function setCurrentBusiness($businessId)
    {
        Session::put('current_business_id', $businessId);
    }
}

if (!function_exists('clearCurrentBusiness')) {
    function clearCurrentBusiness()
    {
        Session::forget('current_business_id');
    }
}
