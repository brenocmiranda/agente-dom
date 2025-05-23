<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AgenteViniciusHoursRqt extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'empreendimento' => 'Empreendimento',
            'data' => 'Data'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'empreendimento' => 'required',
            'data' => 'required', Rule::date()->format('Y-m-d hh:mm')
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {   
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'O campo :attribute deve possuir no minimo :min caracteres.',
        ];    
    }

    /**
     * Return erros in validation
     *
     * @return array<string, string>
     */
    public function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Erros de validação.',
            'data'      => $validator->errors()
        ], 412));
    }
}
