<?php

namespace App\Http\Controllers;

use App\Models\Pharmacist;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Http\Request;

class PharmacistController extends Controller
{

    public function completeRegisterPharm(Request $request) {

        $request->validate([
            'local_address' => 'required|string',
            'certificat_image' => 'required',
            'pharmacy_name' => 'required|string',
            'user_id' => 'required|integer',
        ]);


        $pharmacy = new Pharmacy();

        $pharmacy->pharmacy_name = $request->pharmacy_name;
        $pharmacy->local_address = $request->local_address;

        if($pharmacy->save()) {

           $pharmacist = new Pharmacist();
           $pharmacist->user_id = $request->user_id;

       if ($request->has('certificat_image')) {

          $imageData = $request->input('certificat_image');
          $imageData = str_replace('data:certificat_image/png;base64,', '', $imageData);
          $imageData = str_replace(' ', '+', $imageData);
          $imageData = base64_decode($imageData);
          $imageName = time() . '.png';
          file_put_contents(public_path('pharmacists/' . $imageName), $imageData);
          $pharmacist->certificat_image = asset('pharmacists/'. $imageName);

        }

        $pharmacist->pharmacy_id = $pharmacy->pharmacy_id;
        $pharmacist->save();


        $output = [
            'status' => true,
           'message' => 'Register done successfully',
            'pharmacy' => $pharmacy,
            'pharmacist' => $pharmacist,
        ];


        } else {


            $output = [
                'status' => false,
                'message' => 'Failed to register',
                'data' => null,
            ];



        }

        return response()->json($output);

     }


     public function checkPharmacyName(Request $request) {

        $request->validate([
            'pharmacy_name' => 'required|string',
        ]);

        $pharmacy = Pharmacy::with('pharmacists.user')->where('pharmacy_name', $request->pharmacy_name)->first();

        if($pharmacy) {

            $output = [
                'status' => true,
                'message' => 'The pharmacy name is taken , enter another name',
                // 'pharmacy' => $pharmacy,
            ];

        } else {

            $output = [
                'status' => false,
                'message' => 'The pharmacy name is available',
                // 'pharmacy' => null,
            ];

        }

        return response()->json($output);




     }


     public function checkExistPharmacy(Request $request) {

        $request->validate([
            'pharmacy_name' => 'required|string',
        ]);

        $pharmacy = Pharmacy::with('pharmacists.user')->where('pharmacy_name', $request->pharmacy_name)->first();

        if($pharmacy) {

            $output = [
                'status' => true,
                'message' => 'Pharmacy founded successfully',
                'pharmacy' => $pharmacy,
            ];

        } else {

            $output = [
                'status' => false,
                'message' => 'Pharmacy not found , enter the correct name or register a new pharmacy',
                'pharmacy' => null,
            ];

        }

        return response()->json($output);

     }


     public function completeRegisterSimple(Request $request) {


        $request->validate([
            'certificat_image' => 'required',
            'user_id' => 'required|integer',
            'pharmacy_id' => 'required|integer',
        ]);


        $pharmacist = new Pharmacist();
        $pharmacist->user_id = $request->user_id;

        if ($request->has('certificat_image')) {

           $imageData = $request->input('certificat_image');
           $imageData = str_replace('data:certificat_image/png;base64,', '', $imageData);
           $imageData = str_replace(' ', '+', $imageData);
           $imageData = base64_decode($imageData);
           $imageName = time() . '.png';
           file_put_contents(public_path('pharmacists/' . $imageName), $imageData);
           $pharmacist->certificat_image = asset('pharmacists/'. $imageName);

         }

         $pharmacist->pharmacy_id = $request->pharmacy_id;

            if($pharmacist->save()) {

                $output = [
                    'status' => true,
                    'message' => 'Register done successfully',
                    'pharmacist' => $pharmacist,
                ];

            } else {

                $output = [
                    'status' => false,
                    'message' => 'Failed to register',
                    'data' => null,
                ];

            }

            return response()->json($output);


     }




     public function getProfilePharmacist($user_id) {


        $pharmacist = Pharmacist::where('user_id', $user_id)->first();

        if($pharmacist) {

            $output = [
                'status' => true,
                'message' => 'Pharmacist profile',
                'pharmacist' => $pharmacist,
            ];

        } else {

            $output = [
                'status' => false,
                'message' => 'Pharmacist not found',
                'pharmacist' => null,
            ];

        }

        return response()->json($output);



     }


     public function getPharmacy($pharmacy_id) {


        $pharmacy = Pharmacy::with('pharmacists.user')->where('pharmacy_id' , $pharmacy_id)->first();

         if($pharmacy) {

            $output = [
                'status' => true,
                'message' => 'Pharmacy profile',
                'pharmacy' => $pharmacy,
            ];


         } else {

            $output = [
                'status' => false,
                'message' => 'Pharmacy not found',
                'pharmacy' => null,
            ];

         }


            return response()->json($output);


     }


     public function updateProfilePharmacistWithImage(Request $request , $user_id , $pharmacy_id) {

        $user = User::Where('user_id', $user_id)->first();

        $user->name = $request->name;

        if ($request->has('profile_image')) {


          $imageData = $request->input('profile_image');
          $imageData = str_replace('data:profile_image/png;base64,', '', $imageData);
          $imageData = str_replace(' ', '+', $imageData);
          $imageData = base64_decode($imageData);
          $imageName = time() . '.png';
          file_put_contents(public_path('users/' . $imageName), $imageData);
          $user->profile_image = asset('users/'. $imageName);

        }

         $user->phone = $request->phone;

         $pharmacy = Pharmacy::Where('pharmacy_id', $pharmacy_id)->first();

            $pharmacy->local_address = $request->local_address;
            $pharmacy->pharmacy_name = $request->pharmacy_name;


        if($user->save() && $pharmacy->save()){

            $output = [
                'status' => true,
                'message' => 'Update done successfully',
                'user' => $user,
                'pharmacy' => $pharmacy,
            ];

        }else {


            $output = [
                'status' => false,
                'message' => 'Failed to update, please try again',
                'user' => null,
                'pharmacy' => null,
            ];


        }

        return response()->json($output);

    }


    public function updateProfilePharmacist(Request $request , $user_id , $pharmacy_id) {

        $user = User::Where('user_id', $user_id)->first();

        $user->name = $request->name;

         $user->phone = $request->phone;

         $pharmacy = Pharmacy::Where('pharmacy_id', $pharmacy_id)->first();

            $pharmacy->local_address = $request->local_address;
            $pharmacy->pharmacy_name = $request->pharmacy_name;


        if($user->save() && $pharmacy->save()){

            $output = [
                'status' => true,
                'message' => 'Update done successfully',
                'user' => $user,
                'pharmacy' => $pharmacy,
            ];

        }else {


            $output = [
                'status' => false,
                'message' => 'Failed to update, please try again',
                'user' => null,
                'pharmacy' => null,
            ];


        }

        return response()->json($output);

    }






}
