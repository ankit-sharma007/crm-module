<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('update', $this->route('lead'));
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email,' . $this->route('lead')->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:new,contacted,qualified,closed',
            'notes' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }
}


