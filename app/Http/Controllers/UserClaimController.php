<?php

namespace App\Http\Controllers;

use App\Events\UserRegisterNotification;
use App\Models\UserClaim;
use Illuminate\Http\Request;

class UserClaimController extends Controller
{


    public function addClaim(Request $request) {

        $request->validate([
            'message' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        $claim = new UserClaim();

        $claim->message = $request->message;
        $claim->claim_date = $request->claim_date;
        $claim->user_id = $request->user_id;

        if($claim->save()){

            $output = [
                'status' => true,
                'message' => 'Claim added successfully',
                'data' => $claim,
            ];

            event(new UserRegisterNotification('New User Claim' , $request->body . ' send claim to you'));


        }else {

                $output = [
                    'status' => false,
                    'message' => 'Failed to add claim',
                    'data' => null,
                ];

        }

        return response()->json($output);

    }



    public function getUserClaims() {

        $userClaim = UserClaim::with('user')->orderBy('user_claim_id', 'desc')->get();

        if($userClaim) {

            // PatientClaim::where('doctor_id', $doctor_id)->where('is_read', 'false')->count();

            $output = [
                 'status' => true,
                 'numberClaim' => UserClaim::where('is_read', false)->count(),
                 'userClaims' => $userClaim
            ];


    }

    return response()->json($output);
}



   public function changeStatusUserClaim(Request $request) {

        $userClaim = UserClaim::find($request->user_claim_id);

        $userClaim->is_read = true;


        if($userClaim->save()) {

            $output = [
                'status' => true,
                'message' => 'Claim status changed successfully',
                'userClaim' => $userClaim
            ];
        }

        return response()->json($output);

   }


   public function deleteClaim(Request $request) {


    $userClaim = UserClaim::find($request->user_claim_id);

    if($userClaim->delete()) {

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
