<?php

namespace App\Http\Requests;

use App\Rules\ValidateLatitude;
use App\Rules\ValidateLongitude;
use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lat' => ['required', new ValidateLatitude],
            'long' => ['required', new ValidateLongitude],
        ];
    }
}
