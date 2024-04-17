<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Extension\SmartPunct\EllipsesParser;

use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
    //
    public function registerUser(Request $getreq)
    {

    //    Log::info($getreq->role);

       if($getreq->role=='select')
       {
        return response()->json(['rolefailed'=>true],180);
       }
        $validator = Validator::make($getreq->all(), [
            'email' => 'required|email|unique:users',
            'password' => ['required', 'min:6', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/'],
        ]);

        if($validator->fails())
        {
            return response()->json(['failed'=>true,'errors'=>$validator->errors()->all()],195);
        }
        
        try {
            //code...
        $user=new UserModel();
        $user->email=strtolower($getreq->email);
        $user->password=$getreq->password;
        $user->role=$getreq->role;

        $user->save();
        return response()->json(['success'=>true],200);
        } catch (\Exception $e) {
            return response()->json(['database connection failed'=>true],190);
        }
    }


    public function loginUser(Request $getreq)
    {
        $email=$getreq->email;
        $password=$getreq->password;

        try {
            //code...
            $user = UserModel::where('email', $email)->where('password',$password)->first();
            if (!$user)
            {
            return response()->json(['success'=>false],190);
            }

            

            // $getreq->session()->put('useremail',$email);
            // Log::info($getreq->session()->get('useremail'));
            return response()->json(['success'=>true, 'role'=>$user->role],200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

}
