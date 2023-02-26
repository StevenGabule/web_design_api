<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UpdateDesignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['title' => "string", 'description' => "string", 'tags' => "string", 'team' => "string"])]
    public function rules(): array
    {
        return [
          'title' => 'required|unique:designs,title',
          'description' => 'required|string|min:20|max:140',
          'tags' => 'required',
          'team' => 'required_if:assign_to_team,true'
        ];
    }
}
