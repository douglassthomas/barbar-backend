<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReviewRequest extends FormRequest
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
            //
            'property_id' => 'required',
            'contents' => 'required',
            'user_id' => 'required',
            'cleanliness' => 'required|integer|min:1|max:5',
            'roomf'=>'required|integer|min:1|max:5',
            'publicf'=>'required|integer|min:1|max:5',
            'security'=>'required|integer|min:1|max:5'
        ];
    }
}
