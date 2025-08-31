<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;

class PatientController extends Controller
{

    public function getProfilePatient($user_id) {

        $patient = Patient::with('user')->where('user_id' , $user_id)->first();

            $output = [
                'status' => true,
                'patient' => $patient,
            ];

        return response()->json($output);
    }


    public function getAllCards($patient_id) {

        $cards = Card::with(['doctor.user'])->where('patient_id' , $patient_id)->orderby('card_id' , 'desc')->get();

        $output = [
            'status' => true,
            'cards' => $cards,
        ];

        return response()->json($output);



    }

    public function searchCard(Request $request , $patient_id) {

        $card = Card::with(['doctor.user'])->whereHas('doctor.user' , function($query) use ($request) {
            $query->where('name' , 'like' , '%' . $request->name . '%');
        })->where('patient_id' , $patient_id)->get();


        if($card){

            $output = [
                'status' => true,
                'cards' => $card,
            ];

        }else {

            $output = [
                'status' => false,
                'card' => null,
            ];


        }

        return response()->json($output);


    }


    public function getAllDoctors() {

        $doctors = Doctor::with('user')
        ->join('users', 'doctors.user_id', '=', 'users.user_id')
        ->orderBy('users.name', 'asc')
        ->get();


        // $doctors = Doctor::with('user')->orderBy('user.name' , 'desc')->get();

        $output = [
            'status' => true,
            'doctors' => $doctors,
        ];

        return response()->json($output);
    }

    public function searchDoctor(Request $request) {

        $doctor = Doctor::with('user')->whereHas('user' , function($query) use ($request) {
            $query->where('name' , 'like' , '%' . $request->name . '%');
        })->get();

        $output = [
            'status' => true,
            'doctors' => $doctor,
        ];

        return response()->json($output);


    }

    public function updateProfilePatientWithImage(Request $request , $user_id) {

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

         $patient = Patient::Where('user_id', $user_id)->first();

            $patient->address = $request->address;


        if($user->save() && $patient->save()){

            $output = [
                'status' => true,
                'message' => 'Update done successfully',
                'user' => $user,
                'patient' => $patient,
            ];

        }else {


            $output = [
                'status' => false,
                'message' => 'Failed to update, please try again',
                'user' => null,
                'patient' => null,
            ];


        }

        return response()->json($output);

    }


    public function updateProfilePatient(Request $request , $user_id) {

        $user = User::Where('user_id', $user_id)->first();

        $user->name = $request->name;


         $user->phone = $request->phone;

         $patient = Patient::Where('user_id', $user_id)->first();

         $patient->address = $request->address;


        if($user->save() && $patient->save()){

            $output = [
                'status' => true,
                'message' => 'Update done successfully',
                'user' => $user,
                'patient' => $patient,
            ];

        }else {


            $output = [
                'status' => false,
                'message' => 'Failed to update, please try again',
                'user' => null,
                'patient' => null,
            ];


        }

        return response()->json($output);

    }

}
