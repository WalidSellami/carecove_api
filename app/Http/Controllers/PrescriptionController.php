<?php

namespace App\Http\Controllers;

use App\Events\PushNotification;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{

    public function addPrescription(Request $request) {


        $prescription = new Prescription();
        $prescription->prescription_date = $request->prescription_date;
        $prescription->card_id = $request->card_id;

        if($prescription->save()) {

            $output = [
                'status' => true,
                'message' => 'Done with success',
                'prescription' => $prescription,
            ];

            event(new PushNotification('New Prescription' , $request->body . ' add a prescription in your card', $request->userId));


        }else {

            $output = [
                'status' => false,
                'message' => 'Failed , Try again',
            ];


        }

        return response()->json($output);


    }

    public function getPrescriptions($card_id) {


        $prescription = Prescription::with(['prescriptionMedications.medication'])->with('orders')->where('card_id', $card_id)->orderby('prescription_id' , 'desc')->get();

            $output = [
                'status' => true,
                'prescription' => $prescription,
            ];

        return response()->json($output);


    }


    public function removePrescription(Request $request , $prescription_id) {

        $prescription = Prescription::find($prescription_id);

        if($prescription->delete()) {


            $output = [
               'status' => true,
               'message' => 'Prescription deleted with success',
            ];

            event(new PushNotification('Delete Prescription' , $request->body . ' delete your prescription', $request->userId));

        }else {


            $output = [
               'status' => false,
               'message' => 'Falied , try again',

            ];

        }


      return response()->json($output);

    }


    public function editPrescription(Request $request , $prescription_id) {

       $prescription = Prescription::find($prescription_id);

       $prescription->prescription_date = $request->prescription_date;

         if($prescription->save()) {

          $output = [
                'status' => true,
                'message' => 'Prescription updated with success',
            ];

            event(new PushNotification('Update Prescription' , $request->body . ' update your prescription', $request->userId));


         }else {

            $output = [
                    'status' => false,
                    'message' => 'Falied , try again',
                ];


         }


         return response()->json($output);


    }


}
