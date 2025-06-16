<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Authorization will be handled by the controller middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z0-9\s\.\,\-\_\'\"\!\?]+$/'],
            'description' => ['nullable', 'string', 'regex:/^[A-Za-z0-9\s\.\,\-\_\'\"\!\?]+$/'],
            'status' => ['nullable', 'in:pending,completed'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
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
            'title.regex' => 'Title can only contain letters, numbers, spaces, and basic punctuation.',
            'description.regex' => 'Description can only contain letters, numbers, spaces, and basic punctuation.',
            'due_date.after_or_equal' => 'Due date must be today or in the future.',
        ];
    }
}
