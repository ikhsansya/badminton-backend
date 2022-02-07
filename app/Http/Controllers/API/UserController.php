<?php

namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use App\Models\User;
use App\Http\Resources\UserResource as UserResource;
use Illuminate\Support\Facades\Hash;
use DB;

class UserController extends BaseController
{
    protected $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function index()
    {
        $dataUser = DB::table('users')
                    ->join('levels', 'users.level_id', '=', 'levels.id')
                    ->select('users.*', 'levels.level')
                    ->get();
        return $this->sendResponse($dataUser, 'User data fetched.');
    }

    
    public function store(Request $request)
    {   
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'level_id' => 'required',
        ]);
        

        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }
        $user = new User();
        $dataUser = $this->setUserData($user, $input);
        return $this->sendResponse($dataUser, 'Successfully created new user.');
    }

   
    public function show($id)
    {
        $dataUser = User::find($id);
        if (is_null($dataUser)) {
            return $this->sendError('Data User does not exist.');
        }
        
        return $this->sendResponse($dataUser, 'Data user fetched.');
    }
    

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required',
            'level_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        $this->setUserData($user, $input);
        
        return $this->sendResponse($user, 'Data user updated.');
    }
    
    private function setUserData($userData, $data)
    {
        $userData->name         = $data['name'];
        $userData->email        = $data['email'];
        if(isset($data['password'])){
            $userData->password     = Hash::make($data['password']);
        }
        $userData->level_id     = $data['level_id'];
        $userData->save();

        return $userData;
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return $this->sendResponse($user, 'User deleted.');
    }
}
