<?php

namespace App\Http\Controllers;

use App\Events\PushNotification;
use App\Models\Card;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Pharmacy;
use App\Models\Stock;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DoctorController extends Controller
{

   public function completeRegisterDoc(Request $request) {

    $request->validate([
        'local_address' => 'required|string',
        'specialty'=> 'required|string',
        'certificat_image' => 'required',
        'user_id' => 'required|integer',
    ]);

      $doctor = new Doctor();

      $doctor->local_address = $request->local_address;
      $doctor->specialty = $request->specialty;

    //    if($request->hasFile('certificat_image')){
    //         $photo = $request->file('certificat_image');
    //         $name = time().'.'.$photo->getClientOriginalExtension();
    //         $destinationPath = public_path('doctors');
    //         $photo->move($destinationPath, $name);
    //         //  $imagePath = 'photos/' . $name;
    //          $doctor->certificat_image = asset('doctors/'. $name);

    //     } else

        if ($request->has('certificat_image')) {

        $imageData = $request->certificat_image;
        $imageData = str_replace('data:certificat_image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageData = base64_decode($imageData);
        $imageName = time() . '.png';
        file_put_contents(public_path('doctors/' . $imageName), $imageData);
        $doctor->certificat_image = asset('doctors/'. $imageName);

      }

      $doctor->user_id = $request->user_id;

        if($doctor->save()){

             $output = [
                 'status' => true,
                 'message' => 'Register done successfully',
                 'data' => $doctor,
             ];
        }else {

                $output = [
                    'status' => false,
                    'message' => 'Failed to register',
                    'data' => null,
                ];
        }

        return response()->json($output);

   }

   public function getProfileDoctor($user_id) {

    $doctor = Doctor::with('user')->where('user_id' , $user_id)->first();

    $output = [
        'status' => true,
        'doctor' => $doctor,
    ];

    return response()->json($output);


   }

   public function updateProfileDoctorWithImage(Request $request , $user_id) {

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

     $doctor = Doctor::Where('user_id', $user_id)->first();

        $doctor->local_address = $request->local_address;
        $doctor->specialty = $request->specialty;


    if($user->save() && $doctor->save()){

        $output = [
            'status' => true,
            'message' => 'Update done successfully',
            'user' => $user,
            'doctor' => $doctor,
        ];

    }else {


        $output = [
            'status' => false,
            'message' => 'Failed to update, please try again',
            'user' => null,
            'doctor' => null,
        ];


    }

    return response()->json($output);

}


public function updateProfileDoctor(Request $request , $user_id) {

    $request->validate([
        'name' => 'required|string',
        'local_address' => 'required|string',
        'specialty'=> 'required|string',
        'phone' => 'required'
    ]);

    $user = User::Where('user_id', $user_id)->first();

    $user->name = $request->name;

     $user->phone = $request->phone;

     $doctor = Doctor::Where('user_id', $user_id)->first();

        $doctor->local_address = $request->local_address;
        $doctor->specialty = $request->specialty;


    if($user->save() && $doctor->save()){

        $output = [
            'status' => true,
            'message' => 'Update done successfully',
            'user' => $user,
            'doctor' => $doctor,
        ];

    }else {


        $output = [
            'status' => false,
            'message' => 'Failed to update, please try again',
            'user' => null,
            'doctor' => null,
        ];


    }

    return response()->json($output);

}


   public function addPatientAccount(Request $request) {

     $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:8',
        'role' => 'required|string',
        'phone' => 'required|unique:users',
        'address'=> 'required|string',
     ]);

     try {

        $user = new User();

        $user->name = $request->name;
        $user->role = $request->role;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $password = $request->password;
        $user->password = $password;
        $user->profile_image = 'https://t4.ftcdn.net/jpg/00/64/67/63/360_F_64676383_LdbmhiNM6Ypzb3FM4PPuFP9rHe7ri8Ju.webp';
        $code_auth = rand(100000, 999999);
        $user->code_auth = $code_auth;

        if($user->save()){

        //  $token = $user->createToken('auth_token')->plainTextToken;

         $output = [
            'status' => true,
            'message' => 'Patient added successfully',
            'user' => $user,
            // 'token' => $token,
         ];

         Mail::to($user->email)->send(new \App\Mail\emailMailablePatient($user));


         $patient = new Patient();

         $patient->user_id = $user->user_id;
         $patient->address = $request->address;

         $patient->save();

        }else {

         $output = [
            'status' => false,
            'message' => 'Failed to add patient , please try again',
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


   public function getPatients() {


    $patients = Patient::with('user')
    ->join('users', 'patients.user_id', '=', 'users.user_id')
    ->orderBy('users.name', 'asc')
    ->get();

    $output = [
        'status' => true,
        'message' => 'Patients',
        'patients' => $patients,
    ];

    return response()->json($output);

   }


   public function getPharmacies() {

    $pharmacy = Pharmacy::with(['pharmacists.user'])->orderby('pharmacy_name' , 'asc')->get();

           $output = [
               'status' => true,
               'pharmacies' => $pharmacy,
           ];

       return response()->json($output);


}



public function getAllMedicationsFromStockPharmacy(Request $request) {

    $stock = Stock::with('medication')->where('pharmacy_id' , $request->pharmacy_id)
    ->join('medications', 'stocks.medication_id', '=', 'medications.medication_id')
    ->orderBy('medications.name', 'asc')
    ->get();

    $output = [
      'status' => true,
      'medicationsStock' => $stock,

    ];

    return response()->json($output);


 }

 public function searchMedicationInStockPharmacy(Request $request) {

    $stock = Stock::with('medication')->where('pharmacy_id' , $request->pharmacy_id)->whereHas('medication', function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->name . '%');
    })->get();

    $output = [
      'status' => true,
      'medicationsStock' => $stock,

    ];

    return response()->json($output);


 }



   public function addCard(Request $request) {


         $request->validate([
            'age' => 'required|integer',
            'weight' => 'required',
            'sickness' => 'required|string',
            'patient_id' => 'required|integer',
            'doctor_id' => 'required|integer',
         ]);

         $card = new Card();

            $card->age = $request->age;
            $card->weight = $request->weight;
            $card->sickness = $request->sickness;
            $card->patient_id = $request->patient_id;
            $card->doctor_id = $request->doctor_id;

            if($card->save()){

                $output = [
                    'status' => true,
                    'message' => 'Card added successfully',
                    'card' => $card,
                ];

                event(new PushNotification('New Card' , $request->body . ' add your card', $request->userId));

            }else {

                $output = [
                    'status' => false,
                    'message' => 'Failed to add card , please try again',
                    'card' => null,
                ];

            }

            return response()->json($output);


   }


   public function getCard($doctor_id , $patient_id) {

    $card = Card::with(['patient.user'])->with(['doctor.user'])->where('doctor_id' , $doctor_id)->where('patient_id' , $patient_id)->first();

    $output = [
        'status' => true,
        'message' => 'Cards',
        'card' => $card,
    ];

    return response()->json($output);



   }


   public function updateCard(Request $request) {

    $card = Card::find($request->card_id);

    $card->age = $request->age;
    $card->weight = $request->weight;
    $card->sickness = $request->sickness;

    if($card->save()){

        $output = [
            'status' => true,
            'message' => 'Card updated successfully',
            'card' => $card,
        ];

        event(new PushNotification('Update Card' , $request->body . ' update your card', $request->userId));


    }else {

        $output = [
            'status' => false,
            'message' => 'Failed to update card , please try again',
            'card' => null,
        ];

    }

    return response()->json($output);


   }


   public function deleteCard(Request $request) {

    $card = Card::find($request->card_id);

    if($card->delete()){

        $output = [
            'status' => true,
            'message' => 'Card deleted successfully',
        ];

        event(new PushNotification('Delete Card' , $request->body . ' delete your card', $request->userId));


    }else {

        $output = [
            'status' => false,
            'message' => 'Failed to delete card , please try again',
        ];

    }

    return response()->json($output);

   }


   public function searchPatient(Request $request) {


    $patient = Patient::with('user')->whereHas('user', function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->name . '%');
    })->get();

    if($patient) {

        $output = [
            'status' => true,
            'patients' => $patient,
        ];

    }else {

        $output = [
            'status' => false,
            'message' => 'Failed to find patient , please try again',
            'patients' => null,
        ];

    }


    return response()->json($output);




   }


   public function searchPharmacy(Request $request) {


    $pharmacy = Pharmacy::with(['pharmacists.user'])->where('pharmacy_name' , 'like' , '%' . $request->pharmacy_name . '%')->get();

      if($pharmacy) {


        $output = [
            'status' => true,
            'pharmacies' => $pharmacy,
        ];


      }else {

        $output = [
            'status' => false,
            'message' => 'Failed to find pharmacy , please try again',
            'pharmacies' => null,
        ];

      }

    return response()->json($output);



   }

}
