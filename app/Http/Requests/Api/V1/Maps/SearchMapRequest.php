<?php

namespace App\Http\Requests\Api\V1\Maps;

use Illuminate\Foundation\Http\FormRequest;

class SearchMapRequest extends FormRequest
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
            'game' => 'required|string|in:' . join(',', config('cncnet.games')),
            'search' => 'required|string|min:3',
            'age' => 'nullable|integer|min:0',
        ];
    }
}
