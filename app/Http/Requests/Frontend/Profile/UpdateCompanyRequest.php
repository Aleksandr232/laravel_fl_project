<?php

namespace App\Http\Requests\Frontend\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'inn' => 'required|string|max:50',
            'contact_person' => 'required|string|max:255',
            'phone' => 'nullable|string|max:100',
            'email' => 'required|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название компании',
            'name.max' => 'Название компании не должно превышать 255 символов',
            'inn.required' => 'Введите ИНН',
            'inn.max' => 'ИНН не должен превышать 50 символов',
            'contact_person.required' => 'Введите контактное лицо',
            'contact_person.max' => 'Имя контактного лица не должно превышать 255 символов',
            'phone.max' => 'Номер телефона не должен превышать 100 символов',
            'email.required' => 'Введите email',
            'email.email' => 'Введите корректный email',
            'email.max' => 'Email не должен превышать 255 символов',
        ];
    }
}
