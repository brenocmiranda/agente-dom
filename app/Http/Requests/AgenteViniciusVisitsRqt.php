<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AgenteViniciusVisitsRqt extends FormRequest
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
            'nome' => 'Nome',
            'telefone' => 'Telefone',
            'email' => 'E-mail',
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
            'nome' => 'required|string|min:3',
            'telefone' => 'required|min:3',
            'email' => 'required|email',
            'empreendimento' => 'min:3',
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
            'string' => 'O campo :attribute deve ser preenchido com texto.',
            'email' => 'O campo :attribute só aceita valores de email.',
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
