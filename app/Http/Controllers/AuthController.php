<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:5',
        ]);

        $user = User::create($request->all());

        if ($user) {
            $user->signin = [
                'href'   => 'api/v1/user/signin',
                'method' => 'POST',
                'params' => 'email, password',
            ];

            $response = [
                'msg'  => 'User created',
                'user' => $user,
            ];

            return response()->json($response, 201);
        }

        $response = [
            'msg' => 'An error occured',
        ];

        return response()->json($response, 404);
    }

    public function signin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        
        try{

        }catch(JWTException $e){

        }


        return response()->json($response, 200);
    }
}
