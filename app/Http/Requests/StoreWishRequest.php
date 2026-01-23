<?php

namespace App\Http\Requests;

use App\Services\ThemeService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreWishRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'position' => 'required|integer|min:1|max:10',
        ];
    }
    
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = Auth::user();
            $activeTheme = ThemeService::getActiveTheme() ?: ThemeService::getCurrentYearTheme();
            $currentWishCount = $user->wishesForTheme($activeTheme)->count();
            
            // Check minimum wishes (only when creating the first few)
            if ($currentWishCount < 3 && $this->position > 3) {
                $validator->errors()->add('position', 'You must create at least 3 wishes.');
            }
            
            // Check maximum wishes
            if ($currentWishCount >= 10) {
                $validator->errors()->add('wishes', 'You can only have a maximum of 10 wishes.');
            }
            
            // Check if position is already taken
            if ($user->wishesForTheme($activeTheme)->where('position', $this->position)->exists()) {
                $validator->errors()->add('position', 'A wish already exists at this position.');
            }
        });
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Please enter your wish.',
            'content.max' => 'Your wish cannot exceed 1000 characters.',
            'position.required' => 'Position is required.',
            'position.min' => 'Position must be at least 1.',
            'position.max' => 'Position cannot exceed 10.',
        ];
    }
}
