<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;

class AuthController extends BaseController
{
    public function signin(Request $request){
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            
            $authUser = Auth::user();
            $success['token'] = $authUser->createToken('myAuthApp')->plainTextToken;
            $success['name'] = $authUser->name;
            $success['email'] = $authUser->email;
            $success['level_id'] = $authUser->level_id;

            return $this->sendResponse($success,'Login Success');
        } else {
            return $this->sendError('Login Failed, Unauthorised', ['error' => 'Unauthorised']);
        }
    }

    public function signup(){
        $validator = Validator::make($request->all(), 
        [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyAuthApp')->plainTextToken;
        $success['name'] =  $user->name;
   
        return $this->sendResponse($success, 'User created successfully.');
    }

    public function signout(){
        Auth::logout();
        return response(null, 200);
    }
}
