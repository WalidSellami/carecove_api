<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\UserClaim;
use App\Models\Pharmacist;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    public function getProfileAdmin($user_id)
    {

        $admin = Admin::with('user')->where('user_id', $user_id)->first();

        $output = [
            'status' => true,
            'admin' => $admin,
        ];

        return response()->json($output);
    }

    public function updateProfileAdminWithImage(Request $request, $user_id)
    {

        $user = User::Where('user_id', $user_id)->first();

        $user->name = $request->name;

        if ($request->has('profile_image')) {


            $imageData = $request->input('profile_image');
            $imageData = str_replace('data:profile_image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($imageData);
            $imageName = time() . '.png';
            file_put_contents(public_path('users/' . $imageName), $imageData);
            $user->profile_image = asset('users/' . $imageName);
        }

        $user->phone = $request->phone;

        $admin = Admin::Where('user_id', $user_id)->first();

        $admin->address = $request->address;


        if ($user->save() && $admin->save()) {

            $output = [
                'status' => true,
                'message' => 'Update done successfully',
                'user' => $user,
                'patient' => $admin,
            ];
        } else {


            $output = [
                'status' => false,
                'message' => 'Failed to update, please try again',
                'user' => null,
                'patient' => null,
            ];
        }

        return response()->json($output);
    }


    public function updateProfileAdmin(Request $request, $user_id)
    {

        $user = User::Where('user_id', $user_id)->first();

        $user->name = $request->name;

        $user->phone = $request->phone;

        $admin = Admin::Where('user_id', $user_id)->first();

        $admin->address = $request->address;


        if ($user->save() && $admin->save()) {

            $output = [
                'status' => true,
                'message' => 'Update done successfully',
                'user' => $user,
                'patient' => $admin,
            ];
        } else {


            $output = [
                'status' => false,
                'message' => 'Failed to update, please try again',
                'user' => null,
                'patient' => null,
            ];
        }

        return response()->json($output);
    }



    public function deleteUser($user_id)
    {

        $user = User::where('user_id', $user_id)->first();

        $image = $user->profile_image;

        //  $imageName = basename($image);

        if ($image != null) {

            $imageName = Str::afterLast($image, '/');

            $image_path = public_path('users/' . $imageName);

            if (File::exists(public_path('users/' . $imageName))) {
                // File::delete(public_path('stock/' . $imageName));
                unlink($image_path);
            }
        }

        if ($user->role == 'Doctor') {

            $doctor = Doctor::where('user_id', $user->user_id)->first();

            $certificat = $doctor->certificat_image;

            $certificatName = Str::afterLast($certificat, '/');

            $certificat_path = public_path('doctors/' . $certificatName);

            if (File::exists(public_path('doctors/' . $certificatName))) {
                // File::delete(public_path('doctors/' . $certificatName));
                unlink($certificat_path);
            }
        } else if ($user->role == 'Pharmacist') {

            $pharmacist = Pharmacist::where('user_id', $user->user_id)->first();

            $certificat = $pharmacist->certificat_image;

            $certificatName = Str::afterLast($certificat, '/');

            $certificat_path = public_path('pharmacists/' . $certificatName);

            if (File::exists(public_path('pharmacists/' . $certificatName))) {
                // File::delete(public_path('pharmacists/' . $certificatName));
                unlink($certificat_path);
            }
        }


        if ($user->delete()) {

            $output = [
                'status' => true,
                'message' => 'User deleted successfully',
            ];

            Mail::to($user->email)->send(new \App\Mail\emailReportMailable($user));



        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to delete, try again',
            ];
        }

        return response()->json($output);
    }





    public function addAccount(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'phone' => 'required|unique:users',
            'address' => 'required|string',
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

            if ($user->save()) {

                //  $token = $user->createToken('auth_token')->plainTextToken;

                if ($user->role == 'Admin') {
                    $output = [
                        'status' => true,
                        'message' => 'Admin added successfully',
                        'user' => $user,
                        // 'token' => $token,
                    ];
                } else {
                    $output = [
                        'status' => true,
                        'message' => 'Patient added successfully',
                        'user' => $user,
                        // 'token' => $token,
                    ];
                }


                Mail::to($user->email)->send(new \App\Mail\emailMailablePatient($user));

                if ($user->role === 'Admin') {
                    $admin = new Admin();

                    $admin->user_id = $user->user_id;
                    $admin->address = $request->address;

                    $admin->save();
                } else {

                    $patient = new Patient();

                    $patient->user_id = $user->user_id;
                    $patient->address = $request->address;

                    $patient->save();
                }
            } else {

                $output = [
                    'status' => false,
                    'message' => 'Failed to add patient , please try again',
                    'user' => null,
                ];
            }
        } catch (Exception $e) {

            $output = [
                'status' => 'false',
                'message' => $e->getMessage(),
                'user' => null,
            ];
        }

        return response()->json($output);
    }

    public function getUsers()
    {

        $users = User::with('admin', 'doctor', 'patient', 'pharmacist.pharmacy')->orderby('name', 'Asc')->get();

        if ($users) {

            $output = [
                'status' => true,
                'message' => 'Users retrieved successfully',
                'users' => $users,
            ];
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to retrieve users , please try again',
                'users' => null,
            ];
        }

        return response()->json($output);
    }


    public function getAdmins()
    {

        $admins = Admin::with('user')
            ->join('users', 'users.user_id', '=', 'admins.user_id')
            ->orderby('users.name', 'Asc')
            ->get();

        if ($admins) {

            $output = [
                'status' => true,
                'message' => 'Admins retrieved successfully',
                'admins' => $admins,
            ];
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to retrieve admins , please try again',
                'admins' => null,
            ];
        }

        return response()->json($output);
    }

    public function getDoctors()
    {

        $doctors = Doctor::with('user')
            ->join('users', 'users.user_id', '=', 'doctors.user_id')
            ->orderby('users.name', 'Asc')
            ->get();

        if ($doctors) {

            $output = [
                'status' => true,
                'message' => 'Doctors retrieved successfully',
                'doctors' => $doctors,
            ];
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to retrieve doctors , please try again',
                'doctors' => null,
            ];
        }

        return response()->json($output);
    }

    public function getPharmacists()
    {

        $pharmacists = Pharmacist::with('user')->with('pharmacy')
            ->join('users', 'users.user_id', '=', 'pharmacists.user_id')
            ->orderby('users.name', 'Asc')
            ->get();

        if ($pharmacists) {

            $output = [
                'status' => true,
                'message' => 'Pharmacists retrieved successfully',
                'pharmacists' => $pharmacists,
            ];
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to retrieve pharmacists , please try again',
                'pharmacists' => null,
            ];
        }

        return response()->json($output);
    }


    public function getPatients()
    {

        $patients = Patient::with('user')
            ->join('users', 'users.user_id', '=', 'patients.user_id')
            ->orderby('users.name', 'Asc')
            ->get();

        if ($patients) {

            $output = [
                'status' => true,
                'message' => 'Patients retrieved successfully',
                'patients' => $patients,
            ];
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to retrieve patients , please try again',
                'patients' => null,
            ];
        }

        return response()->json($output);
    }


    public function getUserInfo($user_id)
    {
        $user = User::find($user_id);


        if ($user) {

            if ($user->role == 'Doctor') {

                $doctor = Doctor::where('user_id', $user->user_id)->first();

                $output = [
                    'status' => true,
                    'message' => 'User data retrieved successfully',
                    'user' => $user,
                    'doctor' => $doctor,
                ];
            } else if ($user->role == 'Pharmacist') {


                $pharmacist = Pharmacist::with('pharmacy')->where('user_id', $user->user_id)->first();

                $output = [
                    'status' => true,
                    'message' => 'User data retrieved successfully',
                    'user' => $user,
                    'pharmacist' => $pharmacist,
                ];
            } else if ($user->role == 'Patient') {

                $patient = Patient::where('user_id', $user->user_id)->first();

                $output = [
                    'status' => true,
                    'message' => 'User data retrieved successfully',
                    'user' => $user,
                    'patient' => $patient,
                ];
            } else if ($user->role == 'Admin') {

                $admin = Admin::where('user_id', $user->user_id)->first();

                $output = [
                    'status' => true,
                    'message' => 'User data retrieved successfully',
                    'user' => $user,
                    'admin' => $admin,
                ];
            }
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to get user data',
                'user' => null,
            ];
        }

        return response()->json($output);
    }



    public function searchUser(Request $request) {

        $user = User::with('admin', 'doctor', 'patient', 'pharmacist.pharmacy')->where('name', 'like', '%' . $request->name . '%')->orderby('name', 'Asc')->get();

        if($user) {

            $output = [
                'status' => true,
                'message' => 'User retrieved successfully',
                'users' => $user,
            ];
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to retrieve user , please try again',
                'users' => null,
            ];
        }

        return response()->json($output);


    }


    public function searchAdmin(Request $request) {

        $admin = Admin::with('user')->whereHas('user' , function($query) use ($request) {

         $query->where('name' , 'like' , '%' . $request->name . '%');

        })->get();

        if ($admin) {

            $output = [
                'status' => true,
                'message' => 'Admins retrieved successfully',
                'admins' => $admin,
            ];
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to retrieve admins , please try again',
                'admins' => null,
            ];
        }

        return response()->json($output);


    }


    public function searchDoctor(Request $request) {

        $doctor = Doctor::with('user')->whereHas('user' , function($query) use ($request) {

         $query->where('name' , 'like' , '%' . $request->name . '%');

        })->get();

        if ($doctor) {

            $output = [
                'status' => true,
                'message' => 'Doctors retrieved successfully',
                'doctors' => $doctor,
            ];
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to retrieve admins , please try again',
                'doctors' => null,
            ];
        }

        return response()->json($output);


    }


    public function searchPatient(Request $request) {

        $patient = patient::with('user')->whereHas('user' , function($query) use ($request) {

         $query->where('name' , 'like' , '%' . $request->name . '%');

        })->get();

        if ($patient) {

            $output = [
                'status' => true,
                'message' => 'Patients retrieved successfully',
                'patients' => $patient,
            ];
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to retrieve admins , please try again',
                'patients' => null,
            ];
        }

        return response()->json($output);


    }


    public function searchPharmacist(Request $request) {

        $pharmacist = Pharmacist::with('user')->whereHas('user' , function($query) use ($request) {

         $query->where('name' , 'like' , '%' . $request->name . '%');

        })->get();

        if ($pharmacist) {

            $output = [
                'status' => true,
                'message' => 'Pharmacists retrieved successfully',
                'pharmacists' => $pharmacist,
            ];
        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to retrieve admins , please try again',
                'pharmacists' => null,
            ];
        }

        return response()->json($output);


    }


















    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'password' => 'nullable|string|min:8',
            'role' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->phone = $request->phone;

        // Hash and update the password if provided
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json('User has been updated', 200);
    }

    public function getClaims()
    {
        $claims = UserClaim::orderBy('user_claim_id', 'desc')->with('user')->get();
        return response()->json($claims);
    }

    public function getClaim($user_claim_id)
    {
        $claim = UserClaim::where('user_claim_id', $user_claim_id)->with('user')->get();
        return response()->json($claim);
    }


    public function getNotreadedClaims()
    {
        $claims = UserClaim::where('readed', '=', false)->orderBy('user_claim_id', 'desc')->with('user')->get();
        return response()->json($claims);
    }

    public function updateClaims($user_claim_id)
    {
        try {
            UserClaim::where('user_claim_id', $user_claim_id)
                ->update(['readed' => true]);
        } catch (\Exception $e) {
            throw new \Exception('Error updating claims: ' . $e->getMessage());
        }
    }
}
