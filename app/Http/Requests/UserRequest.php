<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UserRequest extends BaseRequest
{
    public function rules()
    {
        $userId = $this->route('id');

        if ($this->isMethod('post')) {
            return [
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
            ];
        }

        if ($this->isMethod('put')) {
            return [
                'name' => 'sometimes|max:255',
                'email' => [
                    'sometimes',
                    'email',
                    Rule::unique('users', 'email')->ignore($userId, 'id'),

                ],
                'role' => 'sometimes|in:0,1',

            ];
        }

        return [];
    }
}
