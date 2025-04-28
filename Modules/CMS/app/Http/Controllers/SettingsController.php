<?php

namespace Modules\CMS\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CMS\App\Models\Setting;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Get all settings grouped by their group
        $settings = Setting::all()->groupBy('group');
        
        // Get available setting groups
        $groups = Setting::select('group')->distinct()->pluck('group');
        
        return Inertia::render('cms/settings/Index', [
            'settings' => $settings,
            'groups' => $groups
        ]);
    }
    

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'site_logo' => 'nullable|string',
            'site_favicon' => 'nullable|string',
            'site_email' => 'nullable|email',
            'site_phone' => 'nullable|string|max:20',
            'site_address' => 'nullable|string',
            
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'google_site_verification' => 'nullable|string',
            'bing_site_verification' => 'nullable|string',
            'robots_txt' => 'nullable|string',
            
            'facebook_url' => 'nullable|string|url',
            'twitter_url' => 'nullable|string|url',
            'instagram_url' => 'nullable|string|url',
            'linkedin_url' => 'nullable|string|url',
            'youtube_url' => 'nullable|string|url',
            
            'google_analytics_id' => 'nullable|string',
            'facebook_pixel_id' => 'nullable|string',
            
            'custom_header_scripts' => 'nullable|string',
            'custom_footer_scripts' => 'nullable|string',
            'maintenance_mode' => 'boolean',
            'cache_enabled' => 'boolean',
        ]);
        
        // Update or create each setting
        foreach ($data as $key => $value) {
            $group = $this->getSettingGroup($key);
            $type = $this->getSettingType($key, $value);
            
            Setting::setValue($key, $value, $group, $type);
        }
        
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
    
    /**
     * Display settings for a specific group.
     */
    public function group($group)
    {
        $settings = Setting::where('group', $group)->get();
        
        return Inertia::render('cms/settings/Group', [
            'settings' => $settings,
            'group' => $group
        ]);
    }
    
    /**
     * Get the group for a setting based on its key.
     */
    private function getSettingGroup($key)
    {
        $groupMap = [
            // General settings
            'site_name' => 'general',
            'site_description' => 'general',
            'site_logo' => 'general',
            'site_favicon' => 'general',
            'site_email' => 'general',
            'site_phone' => 'general',
            'site_address' => 'general',
            
            // SEO settings
            'meta_title' => 'seo',
            'meta_description' => 'seo',
            'meta_keywords' => 'seo',
            'google_site_verification' => 'seo',
            'bing_site_verification' => 'seo',
            'robots_txt' => 'seo',
            
            // Social media settings
            'facebook_url' => 'social',
            'twitter_url' => 'social',
            'instagram_url' => 'social',
            'linkedin_url' => 'social',
            'youtube_url' => 'social',
            
            // Analytics settings
            'google_analytics_id' => 'analytics',
            'facebook_pixel_id' => 'analytics',
            
            // Advanced settings
            'custom_header_scripts' => 'advanced',
            'custom_footer_scripts' => 'advanced',
            'maintenance_mode' => 'advanced',
            'cache_enabled' => 'advanced',
        ];
        
        return $groupMap[$key] ?? 'general';
    }
    
    /**
     * Get the type for a setting based on its key and value.
     */
    private function getSettingType($key, $value)
    {
        $typeMap = [
            // Boolean settings
            'maintenance_mode' => 'boolean',
            'cache_enabled' => 'boolean',
            
            // Text area settings
            'site_description' => 'textarea',
            'meta_description' => 'textarea',
            'robots_txt' => 'textarea',
            'custom_header_scripts' => 'textarea',
            'custom_footer_scripts' => 'textarea',
            'site_address' => 'textarea',
            
            // URL settings
            'site_logo' => 'url',
            'site_favicon' => 'url',
            'facebook_url' => 'url',
            'twitter_url' => 'url',
            'instagram_url' => 'url',
            'linkedin_url' => 'url',
            'youtube_url' => 'url',
        ];
        
        // Determine type based on value if not in map
        if (!isset($typeMap[$key])) {
            if (is_bool($value)) {
                return 'boolean';
            } elseif (filter_var($value, FILTER_VALIDATE_URL)) {
                return 'url';
            } elseif (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return 'email';
            } elseif (is_numeric($value)) {
                return 'number';
            } elseif (strlen($value) > 255) {
                return 'textarea';
            }
        }
        
        return $typeMap[$key] ?? 'text';
    }
}
