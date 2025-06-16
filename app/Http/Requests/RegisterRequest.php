<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Since registration is open to the public
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'], // Only allow letters and spaces
            'nickname' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9_\-]+$/'], // Letters, numbers, underscore and dash
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/' // Must contain lowercase, uppercase, number, and special character
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.regex' => 'Name can only contain letters and spaces.',
            'nickname.regex' => 'Nickname can only contain letters, numbers, underscores, and dashes.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ];
    }

    /**
     * Get the validator instance for the request.
     * Required for compatibility with RegisterController.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function getValidatorInstance(): Validator
    {
        // Make sure we're working with the actual request data
        $data = $this->all();

        // Debug log to see what data we're getting
        \Log::info('RegisterRequest data:', $data);

        return \Illuminate\Support\Facades\Validator::make(
            $data,
            $this->rules(),
            $this->messages(),
            $this->attributes()
        );
    }
}
