<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_at' => ['nullable', 'date'],
            'priority' => ['required', Rule::in(['high', 'medium', 'low'])],
            'session_minutes' => ['required', 'integer', 'between:5,180'],
            'notify_on_end' => ['boolean'],
            'five_minute_warning' => ['boolean'],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', $this->user()->id)],
        ];
    }
}
