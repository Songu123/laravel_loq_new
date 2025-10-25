<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreClassRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return Auth::check() && $user && $user->isTeacher();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'require_approval' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'tên lớp học',
            'description' => 'mô tả',
            'require_approval' => 'yêu cầu duyệt',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập :attribute.',
            'name.max' => ':attribute không được vượt quá :max ký tự.',
            'description.max' => ':attribute không được vượt quá :max ký tự.',
        ];
    }
}
