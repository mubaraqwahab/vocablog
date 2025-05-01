<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTermRequest extends FormRequest
{
    use TermRequestValidation;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $term = $this->route("term");

        return $term && $this->user()->is($term->owner);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $term = $this->route("term");

        return $this->makeRules($term);
    }
}
