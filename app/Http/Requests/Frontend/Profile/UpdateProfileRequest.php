<?php

namespace App\Http\Requests\Frontend\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:100',
        ];

        // Если передается пароль, добавляем правила валидации
        if ($this->filled('password') && $this->password !== null && trim($this->password) !== '') {
            $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
            $rules['password_confirmation'] = 'required_with:password|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Имя должно быть строкой',
            'name.max' => 'Имя не должно превышать 255 символов',
            'password.required' => 'Введите новый пароль',
            'password.min' => 'Пароль должен содержать минимум 6 символов',
            'password.confirmed' => 'Пароли не совпадают',
            'password_confirmation.required' => 'Подтвердите пароль',
        ];
    }
}
