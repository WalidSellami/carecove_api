<?php

namespace App\Http\Controllers;

use App\Events\pushNotification;
use App\Models\PatientClaim;
use Illuminate\Http\Request;

class PatientClaimController extends Controller
{
    public function addClaimToDoctor(Request $request) {

        $patientClaim = new PatientClaim();

        $patientClaim->message = $request->message;
        $patientClaim->claim_date = $request->claim_date;
        $patientClaim->patient_id = $request->patient_id;
        $patientClaim->doctor_id = $request->doctor_id;


        if($patientClaim->save()) {

            $output = [
                'status' => true,
                'message' => 'Claim sent successfully',
                'data' => $patientClaim
            ];

            event(new PushNotification('New Claim' , $request->body . ' send claim to you', $request->userId));

        } else {

            $output = [
                'status' => false,
                'message' => 'Error , claim not added',
                 'data' => null,
            ];



        }

        return response()->json($output);


    }


    public function getPatientClaims($doctor_id) {

        $patientClaims = PatientClaim::with(['patient.user'])->where('doctor_id', $doctor_id)->orderBy('patient_claim_id', 'desc')->get();

        if($patientClaims) {

            // PatientClaim::where('doctor_id', $doctor_id)->where('is_read', 'false')->count();

            $output = [
                 'status' => true,
                 'numberClaim' => PatientClaim::where('doctor_id', $doctor_id)->where('is_read', 'false')->count(),
                 'patientClaims' => $patientClaims
            ];


    }

    return response()->json($output);
}



   public function changeStatusClaim(Request $request) {

        $patientClaim = PatientClaim::find($request->patient_claim_id);

        $patientClaim->is_read = 'true';


        if($patientClaim->save()) {

            $output = [
                'status' => true,
                'message' => 'Claim status changed successfully',
                 'patientClaim' => $patientClaim
            ];
        }

        return response()->json($output);

   }


   public function deleteClaim(Request $request) {


    $patientClaim = PatientClaim::find($request->patient_claim_id);

    if($patientClaim->delete()) {

        $output = [
            'status' => true,
            'message' => 'Claim deleted successfully',
        ];


    } else {

        $output = [
            'status' => false,
            'message' => 'Error something happen , Try again later',
        ];


    }


    return response()->json($output);

   }


}
