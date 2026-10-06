<?php

namespace App\Features\Posts\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['social', 'news', 'event', 'poll'])],
            'content' => ['nullable', 'string'],
            'media' => ['nullable', 'array'],
            'media.*' => ['nullable'],
            'type_data' => ['required_if:type,event,poll', 'array'],
            'type_data.event_date' => ['required_if:type,event', 'date', 'after:now'],
            'type_data.location_name' => ['required_if:type,event', 'string'],
        ];
    }
}
