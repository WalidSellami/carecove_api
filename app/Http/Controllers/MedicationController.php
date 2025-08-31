<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;

class MedicationController extends Controller
{



   public function checkExistMedication(Request $request) {


    $medication = Medication::where('name', $request->name)->first();

    if($medication) {

        $output = [
            'status' => true,
            'message' => 'Medication already exist',
        ];

    }else {

        $output = [
            'status' => false,
            'message' => 'Medication does not exist',
        ];

    }

    return response()->json($output);


   }

   public function addMedication(Request $request) {


     $request->validate([
         'name' => 'required|string|unique:medications',
         'description' => 'required|string',
     ]);

        $medication = new Medication();

        $medication->name = $request->name;
        $medication->description = $request->description;

        if($medication->save()){

            $output = [
                'status' => true,
                'message' => 'Medication added successfully',
                'medication' => $medication,
            ];

        }else {

            $output = [
                'status' => false,
                'message' => 'Failed to add medication',
                'medication' => null,
            ];

        }

        return response()->json($output);

   }



   public function getAllMedications() {

    $medications = Medication::orderby('name' , 'asc')->get();

    $output = [
        'status' => true,
        'medications' => $medications,
    ];

    return response()->json($output);



   }


   public function editMedication(Request $request) {

      $medication = Medication::where('medication_id' , $request->medication_id)->first();

      $medication->name = $request->name;
      $medication->description = $request->description;

        if($medication->save()) {

            $output = [
                'status' => true,
                'message' => 'Medication updated successfully',
                'medication' => $medication,
            ];

        }else {

            $output = [
                'status' => false,
                'message' => 'Failed to update medication',
                'medication' => null,
            ];

        }

        return response()->json($output);


   }

   public function searchMedication(Request $request) {

    $medications = Medication::where('name', 'like', '%' . $request->name . '%')->get();

    $output = [
        'status' => true,
        'medications' => $medications,
    ];

    return response()->json($output);


   }

}
