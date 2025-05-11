<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'cms_settings';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'options',
        'is_public',
        'description'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'value' => 'json',
        'options' => 'json',
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string $type
     * @param array $options
     * @param boolean $is_public
     * @param string $description
     * @return Setting
     */
    public static function setValue(string $key, $value, string $group = 'general', string $type = 'text', array $options = [], bool $is_public = false, string $description = '')
    {
        $setting = self::firstOrNew(['key' => $key]);
        
        $setting->value = $value;
        $setting->group = $group;
        $setting->type = $type;
        $setting->options = $options;
        $setting->is_public = $is_public;
        $setting->description = $description;
        
        $setting->save();
        
        return $setting;
    }

    /**
     * Get all settings by group
     *
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByGroup(string $group)
    {
        return self::where('group', $group)->get();
    }

    /**
     * Get all public settings
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPublic()
    {
        return self::where('is_public', true)->get();
    }
}
