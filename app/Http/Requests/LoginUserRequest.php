<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
class LoginUserRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		if($this->user()) return false; // They're already signed in, so they shouldn't be logging in
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
			'email' => 'required',
			'password' => 'required',
			'remember' => 'nullable|bool',
		];
	}
}
