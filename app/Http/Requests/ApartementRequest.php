<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApartementRequest extends FormRequest
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
            'name' => 'required',//
            'description' => 'min:8',//
            'pictures' => '',
            'picture360' => '',//
            'floor' => '',//
            'video' => '',//
            'roomf' => '',
            'area' => '',
            'publicf' => '',
            'unit_condition' => '',
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
            'unit_type' => '',
            'banner' => '',
            'longitude' => 'required',
            'latitude' => 'required'
        ];
    }
}
