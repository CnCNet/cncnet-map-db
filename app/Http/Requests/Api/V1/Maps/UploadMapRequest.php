<?php

namespace App\Http\Requests\Api\V1\Maps;

use Illuminate\Foundation\Http\FormRequest;

class UploadMapRequest extends FormRequest
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
            'file' => [
                'required', 'file', 'max:1048576', 'mimes:zip',
                function ($attribute, $value, $fail) {
                    // Get the original filename
                    $originalName = $value->getClientOriginalName();

                    // Validate that the filename is a SHA1 hash and ends with .zip
                    if (!preg_match('/^[a-f0-9]{40}\.zip$/i', $originalName)) {
                        $fail('File name not valid.');
                    }
                }
            ]
        ];
    }
}
