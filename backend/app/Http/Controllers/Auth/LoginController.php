<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $this->validateRequest($request, 'login');

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token, 'user' => $user], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {
        $data = $request->all();
        /* $this->validateRequest($request, 'register'); */


        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return response()->json(['message' => 'Usuário registrado com sucesso', 'user' => $user], 200);
    }

    private function validateRequest(Request $request, $type)
    {
        $rules = [];

        switch ($type) {
            case 'login':
                $rules = [
                    'email' => 'required|email',
                    'password' => 'required'
                ];
                break;

            case 'register':
                $rules = [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8',
                ];
                break;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            response()->json(['error' => $validator->errors()], 422)->send();
            exit;
        }
    }
}
