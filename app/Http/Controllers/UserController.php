<?php

namespace App\Http\Controllers;

use App\Events\UserRegisterNotification;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{

    public function register(Request $request) {

       $request->validate([
           'name' => 'required|string',
           'email' => 'required|email|unique:users',
           'password' => 'required|string|min:8',
           'role' => 'required',
           'phone' => 'required|unique:users',
       ]);

       try {

        $user = new User();

        $user->name = $request->name;
        $user->role = $request->role;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $password = app('hash')->make($request->password);
        $user->password = $password;
        $user->profile_image = 'https://t4.ftcdn.net/jpg/00/64/67/63/360_F_64676383_LdbmhiNM6Ypzb3FM4PPuFP9rHe7ri8Ju.webp';
        $code_auth = rand(100000, 999999);
        $user->code_auth = $code_auth;

        if($user->save()){

         $token = $user->createToken('auth_token')->plainTextToken;

         $output = [
            'status' => true,
            'message' => 'Done with success',
            'user' => $user,
            'token' => $token,
         ];

         Mail::to($user->email)->send(new \App\Mail\emailMailable($user));
         event(new UserRegisterNotification('New User' , $user->role . '  ' . $user->name));

        }else {

         $output = [
            'status' => false,
            'message' => 'Error occured while creating account , please try again',
            'user' => null,
         ];

        }


       }catch (Exception $e) {

        $output = [
           'status' => 'false',
           'message' => $e->getMessage(),
           'user' => null,
        ];

       }

       return response()->json($output);

    }

    public function login(Request $request) {

       $request->validate([
           'email' => 'required|email',
           'password' => 'required|string|min:8',
       ]);

       $user = User::where('email', $request->email)->first();

       if(!$user){
        $output = [
            'status' => false,
            'message' => 'Failed to login , you are not registered',
            'user' => null,
            'token' => null,
        ];
       }else if(app('hash')->check($request->password, $user->password) || ($request->password == $user->password)) {
        $token = $user->createToken('auth_token')->plainTextToken;
        $output = [
            'status' => true,
            'message' => 'Login done successfully',
            'user' => $user,
            'token' => $token,
        ];

       }else {
        $output = [
            'status' => false,
            'message' => 'Failed to login , email or password is incorrect',
            'user' => null,
            'token' => null,
     ];

    }

      return response()->json($output);
    }




    public function profile() {

        $user = Auth::user();

        $output = [
            'status' => true,
            'user' => $user,
        ];

        return response()->json($output);

    }





    // public function update(Request $request , $user_id) {

    //     $request->validate([
    //         'name' => 'required|string',
    //         'profile_image' => 'required',
    //         'phone' => 'required',
    //     ]);

    //     $user = User::Where('user_id', $user_id)->first();

    //     $user->name = $request->name;

        // if($request->hasFile('profile_image')){
        //     $photo = $request->file('profile_image');
        //     $name = time().'.'.$photo->getClientOriginalExtension();
        //     $destinationPath = public_path('users');
        //     $photo->move($destinationPath, $name);
        //     //  $imagePath = 'photos/' . $name;
        //      $user->profile_image = asset('users/'. $name);

        // } else

    //     if ($request->has('profile_image')) {


    //       $imageData = $request->input('profile_image');
    //       $imageData = str_replace('data:profile_image/png;base64,', '', $imageData);
    //       $imageData = str_replace(' ', '+', $imageData);
    //       $imageData = base64_decode($imageData);
    //       $imageName = time() . '.png';
    //       file_put_contents(public_path('users/' . $imageName), $imageData);
    //       $user->profile_image = asset('users/'. $imageName);

    //     }

    //      $user->phone = $request->phone;


    //     if($user->save()){

    //         $output = [
    //             'status' => true,
    //             'message' => 'Update done successfully',
    //             'user' => $user,
    //         ];

    //     }else {


    //         $output = [
    //             'status' => false,
    //             'message' => 'Failed to update, please try again',
    //             'user' => null,
    //         ];


    //     }

    //     return response()->json($output);

    // }





    public function changePassword(Request $request , $user_id) {

        $user = User::Where('user_id', $user_id)->first();

        $user->password = app('hash')->make($request->password);

        if($user->save()){

            $output = [
                'status' => true,
                'message' => 'Password changed successfully',
                'user' => $user,
            ];

        }else {

            $output = [
                'status' => false,
                'message' => 'Failed to change password, please try again',
                'user' => null,
            ];

    }

        return response()->json($output);

}




    public function logout(Request $request){

        $user = User::where('user_id' , $request->user_id)->first();

        // $user = Auth::user();

        $user->tokens()->delete();


        $output = [
            'status' => true,
            'message' => 'Logout done successfully',
        ];

        return response()->json($output);

    }


    public function updateCodeAuth($user_id){

        $user = User::where('user_id' , $user_id)->first();
        $code_auth = rand(100000, 999999);
        $user->code_auth = $code_auth;

        Mail::to($user->email)->send(new \App\Mail\emailNewMailable($user));

        if($user->save()){

            $output = [
                'status' => true,
                'message' => 'Code send with success',
                'code_auth' => $code_auth,
            ];

        }else {

            $output = [
                'status' => false,
                'message' => 'Error , please try again',
                'code_auth' => null,
            ];

        }

        return response()->json($output);


    }



    public function checkEmail(Request $request) {

        $user = User::where('email' , $request->email)->first();

        if(!$user){

            $output = [
                'status' => false,
                'message' => 'Failed to find your email , you are not registered',
                'email' => null,
                'code_auth' => null,
            ];

        } else {

            $code_auth = rand(100000, 999999);
            $user->code_auth = $code_auth;

            $output = [
                'status' => true,
                'message' => 'Email found successfully',
                'email' =>  $user->email,
                'code_auth' => $code_auth,
            ];

            Mail::to($user->email)->send(new \App\Mail\emailMailableResetPassword($user));

        }

        return response()->json($output);

    }




    public function resetPassword(Request $request , $email) {

        $user = User::where('email' , $email)->first();

           $user->password = app('hash')->make($request->password);

              if($user->save()){

                $output = [
                 'status' => true,
                 'message' => 'Password reseted successfully',
                 'user' => $user,
                ];

            } else {

                $output = [
                 'status' => false,
                 'message' => 'Failed to reset password, please try again',
                 'user' => null,
                ];
            }

        return response()->json($output);

    }


    public function deleteAccount(Request $request) {


        $user = User::where('user_id' , $request->user_id)->first();

        if($user->delete()) {

            $output = [
                'status' => true,
                'message' => 'Account deleted successfully',
            ];


    } else {

        $output = [
            'status' => false,
            'message' => 'Failed to delete account, please try again',
        ];

    }

    return response()->json($output);

}


 public function checkAccount(Request $request) {

        $user = User::where('user_id' , $request->user_id)->first();

        if($user) {

            $output = [
                'status' => true,
                'message' => 'User is registered',
                'user' => $user,
            ];

        } else {

            $output = [
                'status' => false,
                'message' => 'User is not registered',
                'user' => null,
            ];

        }

        return response()->json($output);

    }


 }

