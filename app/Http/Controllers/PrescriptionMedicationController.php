<?php

namespace App\Http\Controllers;

use App\Events\PushNotification;
use App\Models\PrescriptionMedication;
use Illuminate\Http\Request;

class PrescriptionMedicationController extends Controller
{


   public function addMedicationInPrescription(Request $request) {

    $request->validate([
        'prescription_id' => 'required|integer',
        'medication_id' => 'required|integer',
        'dosage' => 'required',
        'quantity' => 'required',
    ]);

    $prescriptionMedication = new PrescriptionMedication();
    $prescriptionMedication->prescription_id = $request->prescription_id;
    $prescriptionMedication->medication_id = $request->medication_id;
    $prescriptionMedication->dosage = $request->dosage;
    $prescriptionMedication->quantity = $request->quantity;


    if($prescriptionMedication->save()) {


        $output = [
           'status' => true,
            'message' => 'Done with success',
        ];

        event(new PushNotification('New Medication Prescription' , $request->body . ' add medication in your prescription', $request->userId));


    }else {


        $output = [
           'status' => false,
           'message' => 'Failed , Try again',

        ];

    }


    return response()->json($output);

   }


   public function getMedicationsFromPrescription($prescription_id) {


    // $medications = PrescriptionMedication::with('medication' , 'prescription')->select('medication_id' , 'dosage')->whereHas('prescription', function ($query) use ($prescription_id) {
    //     $query->select('prescription_date')->where('prescription_id' , $prescription_id);
    // })->get();


    $medications = PrescriptionMedication::with('medication')->select('medication_id' , 'dosage')->where('prescription_id' , $prescription_id)->get();

    $output = [
        'status' => true,
        'medicationsPrescription' => $medications,
    ];


    return response()->json($output);

   }


   public function editMedicationPrescription(Request $request , $prescription_medication_id) {

    $prescriptionMedication = PrescriptionMedication::find($prescription_medication_id);

    $prescriptionMedication->dosage = $request->dosage;
    $prescriptionMedication->quantity = $request->quantity;

    if($prescriptionMedication->save()) {


        $output = [
          'status'=> true,
          'message' => 'Update done successfully',

        ];

        event(new PushNotification('Edit Dosage Medication' , $request->body . ' update your dosage medication', $request->userId));


    }else {

        $output = [
          'status' => false,
          'message' => 'Failed to update , Try again',


        ];

    }

    return response()->json($output);


   }

   public function checkExistMedicationInPrescription(Request $request) {


    $request->validate([
        'prescription_id' => 'required|integer',
        'medication_id' => 'required|integer',
    ]);

    $medication = PrescriptionMedication::where('prescription_id' , $request->prescription_id)->where('medication_id' , $request->medication_id)->first();

    if($medication) {

        $output = [
            'status' => true,
            'message' => 'Medication already exist in prescription',
        ];

    }else {

        $output = [
            'status' => false,
            'message' => 'Medication not exist in prescription',
        ];

    }

    return response()->json($output);




   }


   public function removeMedicationFromPrescription(Request $request , $prescription_medication_id) {


    $prescriptionMedication = PrescriptionMedication::find($prescription_medication_id);

    if($prescriptionMedication->delete()) {

        $output = [
            'status' => true,
            'message' => 'Medication removed successfully',
        ];

        event(new PushNotification('Remove Medication Prescription' , $request->body . ' remove medication from your prescription', $request->userId));

    }else {

        $output = [
            'status' => false,
            'message' => 'Failed to remove medication , Try again',
        ];

    }

    return response()->json($output);

   }


}
