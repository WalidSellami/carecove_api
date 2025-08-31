<?php

namespace App\Http\Controllers;

use App\Events\PushNotification;
use App\Models\Medication;
use App\Models\Pharmacy;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class StockController extends Controller
{

   public function addMedicationInStock(Request $request) {

     $request->validate([
         'quantity' => 'required|integer',
         'medication_image' => 'required',
         'date_of_manufacture' => 'required',
         'date_of_expiration' => 'required',
         'medication_id' => 'required|integer',
         'pharmacy_id' => 'required|integer',
     ]);

        $stock = new Stock();


        $stock->quantity = $request->quantity;

        if ($request->has('medication_image')) {

            $imageData = $request->input('medication_image');
            $imageData = str_replace('data:medication_image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($imageData);
            $imageName = time() . '.png';
            file_put_contents(public_path('stock/' . $imageName), $imageData);
            $stock->medication_image = asset('stock/'. $imageName);

          }

        $stock->date_of_manufacture = $request->date_of_manufacture;
        $stock->date_of_expiration = $request->date_of_expiration;
        $stock->medication_id = $request->medication_id;
        $stock->pharmacy_id = $request->pharmacy_id;

        if($stock->save()){

            $output = [
                'status' => true,
                'message' => 'Medication added successfully',
                'medication' => $stock,
            ];

        event(new PushNotification('New Medication Stock' , $request->body . ' add new medication to the stock', $request->userId));


        }else {

            $output = [
                'status' => false,
                'message' => 'Failed to add medication',
                'medication' => null,
            ];

        }

        return response()->json($output);


   }



   public function getAllMedicationsFromStock($pharmacy_id) {


    $stock = Stock::with('medication')->where('pharmacy_id' , $pharmacy_id)
    ->join('medications', 'stocks.medication_id', '=', 'medications.medication_id')
    ->orderBy('medications.name', 'asc')
    ->get();

    
      $output = [
        'status' => true,
        'medicationsStock' => $stock,

      ];

      return response()->json($output);


   }


   public function editMedicationStock(Request $request) {

    $stock = Stock::where('stock_id' , $request->stock_id)->first();

    $stock->quantity = $request->quantity;

    $stock->date_of_manufacture = $request->date_of_manufacture;
    $stock->date_of_expiration = $request->date_of_expiration;

    $medication = Medication::where('medication_id' , $stock->medication_id)->first();


   if($stock->save()) {

      $output = [
        'status' => true,
        'message' => 'Update done successfully'
      ];


      event(new PushNotification('Edit Medication Stock' , $request->body . ' edit ' . $medication->name . ' in the stock', $request->userId));



   }else {

      $output = [
          'status' => false,
          'message' => 'Failed to update, please try again'

        ];

   }

   return response()->json($output);

   }





   public function editMedicationStockWithImage(Request $request) {


      $stock = Stock::where('stock_id' , $request->stock_id)->first();

      $stock->quantity = $request->quantity;

      if ($request->has('medication_image')) {

        $imageData = $request->input('medication_image');
        $imageData = str_replace('data:medication_image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageData = base64_decode($imageData);
        $imageName = time() . '.png';
        file_put_contents(public_path('stock/' . $imageName), $imageData);
        $stock->medication_image = asset('stock/'. $imageName);

      }

      $stock->date_of_manufacture = $request->date_of_manufacture;
      $stock->date_of_expiration = $request->date_of_expiration;

      $medication = Medication::where('medication_id' , $stock->medication_id)->first();



     if($stock->save()) {

        $output = [
          'status' => true,
          'message' => 'Update done successfully'

        ];

        event(new PushNotification('Edit Medication Stock' , $request->body . ' edit ' . $medication->name .  ' in the stock', $request->userId));



     }else {

        $output = [
            'status' => false,
            'message' => 'Failed to update, please try again'

          ];

     }

     return response()->json($output);



   }


   public function removeMedicationStock(Request $request) {

     $stock = Stock::where('stock_id' , $request->stock_id)->first();

     $medication = Medication::where('medication_id' , $stock->medication_id)->first();


     $image = $stock->medication_image;

    //  $imageName = basename($image);

     $imageName = Str::afterLast($image, '/');

     $image_path = public_path('stock/' . $imageName);

     if(File::exists(public_path('stock/' . $imageName))){
        // File::delete(public_path('stock/' . $imageName));
        unlink($image_path);
     }

       // if (Storage::disk('public')->exists('stock/' . $imageName)) {
        //     Storage::disk('public')->delete('stock/' . $imageName);
        // }

     if($stock->delete()) {


        $output = [
            'status' => true,
            'message' => 'Medication deleted successfully',
        ];

        event(new PushNotification('Delete Medication Stock' , $request->body . ' delete ' . $medication->name . ' from the stock', $request->userId));



     }else {

        $output = [
            'status' => false,
            'message' => 'Failed to delete medication , please try again',
        ];


     }

     return response()->json($output);


   }



   public function checkExistMedicationInStock(Request $request) {


        $stock = Stock::where('medication_id' , $request->medication_id)->where('pharmacy_id' , $request->pharmacy_id)->first();

        if($stock) {

            $output = [
                'status' => true,
                'message' => 'Medication already exist in stock',
            ];

        }else {

            $output = [
                'status' => false,
                'message' => 'Medication not exist in stock',
            ];

        }

        return response()->json($output);



   }



   public function getPharmaciesHaveMedicationsPrescription($medications , $quantities) {

        $medicationIds = explode(',', $medications);
        $quantitiesIds = explode(',', $quantities);

        $pharmacies = Pharmacy::with(['pharmacists.user'])->where(function ($query) use ($medicationIds) {
            foreach ($medicationIds as $medicationId) {
                $query->whereHas('stocks', function ($query) use ($medicationId) {
                    $query->where('medication_id', '=', $medicationId);
                });
            }

        })->where(function ($query) use ($quantitiesIds) {
            foreach ($quantitiesIds as $quantityId) {
                $query->whereHas('stocks', function ($query) use ($quantityId) {
                    $query->where('quantity', '>=', $quantityId);
                });
            }
        })->get();


        $output = [
            'status' => true,
            'pharmacies' => $pharmacies,
        ];

   return response()->json($output);


   }


   public function decrementQuantityMedications($medications , $quantities , $pharmacy_id) {


    $medicationIds = explode(',', $medications);
    $quantitiesIds = explode(',', $quantities);

    foreach ($medicationIds as $key => $medicationId) {

        $stock = Stock::where('medication_id' , $medicationId)->where('pharmacy_id' , $pharmacy_id)->first();

        $stock->decrement('quantity' , $quantitiesIds[$key]);

        $stock->save();

    }

    $output = [
        'status' => true,
        'message' => 'Medications decremented successfully',
    ];

    return response()->json($output);


   }


   public function searchMedicationInStock(Request $request) {


    $stock = Stock::with('medication')->where('pharmacy_id' , $request->pharmacy_id)->whereHas('medication', function ($query) use ($request) {
        $query->where('name', 'like', '%' . $request->name . '%');
    })->get();

    $output = [
        'status' => true,
        'medicationsStock' => $stock,
    ];

    return response()->json($output);



   }

}
