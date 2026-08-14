<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrackerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'activity_id' => ['required', 'exists:activities,id'],
            'update_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['done', 'pending'])],
            'remark' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
