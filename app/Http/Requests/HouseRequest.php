<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class HouseRequest extends FormRequest
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
            //ikutin yg dri front
            'name' => 'required',//
            'description' => 'min:8',//
            'pictures' => '',
            'picture360' => '',//
            'room_left' => '',//
            'video' => '',//
            'roomf' => '',
            'area' => '',
            'publicf' => '',
            'parking' => '',
            'information' => '',
            'fee' => '',
            'price' => 'required|min:1',
            'price_year' => '',
            'price_month' => '',
            'price_week' => '',
            'price_day' => '',

            'owner_id' => 'required',
            'city_id' => 'required',
            'address' => 'required',
            'gender' => '',
            'banner' => '',
            'longitude' => 'required',
            'latitude' => 'required'

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors()));
    }
}
