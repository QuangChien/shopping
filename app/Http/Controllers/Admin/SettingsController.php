<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        // Get all setting groups
        $groupNames = Setting::select('group_name')
            ->distinct()
            ->pluck('group_name')
            ->toArray();
        
        $settings = [];
        
        // Get settings for each group
        foreach ($groupNames as $groupName) {
            $settings[$groupName] = Setting::where('group_name', $groupName)
                ->orderBy('key')
                ->get();
        }
        
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
            'groupNames' => $groupNames,
        ]);
    }
    
    /**
     * Update settings
     */
    public function update(Request $request)
    {
        try {
            // Basic validation for the structure
            $data = $request->validate([
                'settings' => 'required|array',
                'settings.*.id' => 'required|exists:settings,id',
                'settings.*.value' => 'present',
            ]);

            // Additional validation for each setting
            foreach ($data['settings'] as $index => $settingData) {
                $setting = Setting::find($settingData['id']);
                
                if ($setting) {
                    $value = $settingData['value'];
                    $rules = [];
                    $customMessages = [];
                    
                    // Set validation rules based on setting type
                    switch ($setting->type) {
                        case 'number':
                            $rules["settings.{$index}.value"] = 'numeric';
                            $customMessages["settings.{$index}.value.numeric"] = "The {$setting->key} must be a number.";
                            break;
                            
                        case 'integer':
                            $rules["settings.{$index}.value"] = 'integer';
                            $customMessages["settings.{$index}.value.integer"] = "The {$setting->key} must be an integer.";
                            break;
                            
                        case 'email':
                            $rules["settings.{$index}.value"] = 'email';
                            $customMessages["settings.{$index}.value.email"] = "The {$setting->key} must be a valid email address.";
                            break;
                            
                        case 'url':
                            $rules["settings.{$index}.value"] = 'url';
                            $customMessages["settings.{$index}.value.url"] = "The {$setting->key} must be a valid URL.";
                            break;
                            
                        case 'select':
                            if (!empty($setting->options)) {
                                $options = json_decode($setting->options, true);
                                if (is_array($options)) {
                                    $rules["settings.{$index}.value"] = Rule::in(array_keys($options));
                                    $customMessages["settings.{$index}.value.in"] = "The selected {$setting->key} is invalid.";
                                }
                            }
                            break;
                    }
                    
                    // Check if setting is required
                    if ($setting->required) {
                        $rules["settings.{$index}.value"] = array_merge(
                            ['required'], 
                            is_array($rules["settings.{$index}.value"] ?? null) 
                                ? $rules["settings.{$index}.value"] 
                                : ($rules["settings.{$index}.value"] ? [$rules["settings.{$index}.value"]] : [])
                        );
                        
                        $customMessages["settings.{$index}.value.required"] = "The {$setting->key} field is required.";
                    }
                    
                    // Validate if rules exist
                    if (!empty($rules)) {
                        $validator = Validator::make($request->all(), $rules, $customMessages);
                        
                        if ($validator->fails()) {
                            throw new ValidationException($validator);
                        }
                    }
                    
                    // Format value based on type
                    if ($setting->type === 'boolean') {
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                        $value = $value ? 'true' : 'false';
                    } elseif ($setting->type === 'number' || $setting->type === 'integer') {
                        $value = (string) $value;
                    }
                    
                    $setting->value = $value;
                    $setting->save();
                }
            }
            
            // Clear cache
            Setting::clearCache();
            
            return back()->with('success', trans('admin.settings.updated'));
        } catch (ValidationException $e) {
            return back()->with('error', trans('admin.settings.validation_error'))->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', trans('admin.settings.update_error'));
        }
    }
    
    /**
     * Get settings by group
     */
    public function getByGroup(Request $request, $group)
    {
        try {
            $settings = Setting::where('group_name', $group)
                ->orderBy('key')
                ->get();
                
            return response()->json($settings);
        } catch (\Exception $e) {
            return response()->json(['error' => trans('admin.settings.update_error')], 500);
        }
    }
} 