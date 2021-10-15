<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // En caso de usar Auth

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);
        
        /* $user = User::created([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]); */

        $user = new User;
            $user->name = $fields['name'];
            $user->email = $fields['email'];
            $user->password = Hash::make($fields['password']);
        $user->save();

        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response ,201);
    }

    public function login(Request $request)
    {
        // Check Fields
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check User
        $user = User::where('email', $fields['email'])->first();
        
        // Check Password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            
            return response([
                'msj' => 'Invalid Credentials'
            ], 401);
        }

        /* if (!Auth::attempt($request->only('email', 'password'))) {
            
            $response = [
                'msj' => 'Invalid login details'
            ];
    
            return response($response, 401);
        }*/

        $user = User::where('email', $fields['email'])->firstOrFail();
        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response); 
    }

    public function logout()
    {
       auth()->user()->tokens()->delete();

        return [
            'msj' => 'Logged out'
        ];
    }

    public function infouser(Request $request)
    {
        return $request->user();
    }
}


