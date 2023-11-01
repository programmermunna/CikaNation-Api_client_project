<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'announcements'           => 'required|array|min:1',
            'announcements.*.id'      => 'required|exists:announcements,id', 
            'announcements.*.message' => 'required|string|max:255', 
            'announcements.*.status'  => 'required|boolean', 
        ];
    }
}
