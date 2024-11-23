<?php

namespace App\Http\Requests\API;

use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'position_id' => ['required', 'exists:'.Position::class.',id'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg', 'max:5120'],
            'phone' => ['required', 'string', 'regex:/^\+380\d{9}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
