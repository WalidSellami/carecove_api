<?php

namespace App\Http\Controllers;

use App\Events\PushNotification;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

   public function sendOrderToPharmacy(Request $request) {

      $order = new Order();
      $order->order_date = $request->order_date;
      $order->prescription_id = $request->prescription_id;
      $order->pharmacy_id = $request->pharmacy_id;

      if($order->save()) {

         $output = [
            'status' => true,
            'message' => 'Order sent successfully',
            'order' => $order,

         ];

         event(new PushNotification('New Order' , $request->body . ' send order to you', $request->userId));

      }else {

            $output = [
                'status' => false,
                'message' => 'Failed to send order',
                'order' => null,

            ];

      }

      return response()->json($output);

   }


   public function getOrders($pharmacy_id) {


    $order = Order::with('pharmacy')->with(['prescription.prescriptionMedications.medication'])->with(['prescription.card.patient.user'])->with(['prescription.card.doctor.user'])->where('pharmacy_id', $pharmacy_id)->orderby('order_id' , 'desc')->get();

        $output = [
            'status' => true,
            'numberOrders' => Order::where('pharmacy_id', $pharmacy_id)->where('is_read', 'false')->count(),
            'message' => 'Orders retrieved successfully',
            'orders' => $order,

        ];

        return response()->json($output);


   }


   public function checkOrderIsSended(Request $request) {


      $order = Order::where('prescription_id' , $request->prescription_id)->first();

      if($order) {

        $output = [
            'status' => true,
            'message' => 'Order is already sended',
        ];

      } else {

        $output = [
            'status' => false,
            'message' => 'Order is not sended',
        ];


      }

      return response()->json($output);

   }




   public function acceptOrder(Request $request) {


        $order = Order::find($request->order_id);

        $order->status = 'Accepted';
        $order->is_read = 'true';

        if($order->save()) {

             $output = [
                'status' => true,
                'order' => $order,

             ];

         event(new PushNotification('Status Order' , $request->body . ' accept your order', $request->userId));


        }else {

                $output = [
                    'status' => false,
                    'message' => 'Failed to change order status',
                    'order' => null,

                ];

        }

        return response()->json($output);


   }




   public function refuseOrder(Request $request) {


    $order = Order::find($request->order_id);

    $order->status = 'Refused';
    $order->is_read = 'true';

    if($order->save()) {

         $output = [
            'status' => true,
            'order' => $order,

         ];

         event(new PushNotification('Status Order' , $request->body . ' refuse your order', $request->userId));

    }else {

            $output = [
                'status' => false,
                'message' => 'Failed to change order status',
                'order' => null,

            ];

    }

    return response()->json($output);


}


  public function getPatientOrders($patient_id) {


    $order = Order::with('pharmacy')->with(['prescription.prescriptionMedications.medication'])->with(['prescription.card.patient.user'])->with(['prescription.card.doctor.user'])->whereHas('prescription.card.patient', function($q) use($patient_id) {
        $q->where('patient_id', $patient_id);
    })->orderby('order_id' , 'desc')->get();

        $output = [
            'status' => true,
            'message' => 'Orders retrieved successfully',
            'orders' => $order,

        ];

        return response()->json($output);

  }



  public function removeOrder($prescription_id) {

    $order = Order::where('prescription_id', $prescription_id)->where('status' , 'Refused')->first();

    if($order) {

        if($order->delete()) {

            $output = [
                'status' => true,
                'message' => 'Order removed successfully',
            ];

        } else {

            $output = [
                'status' => false,
                'message' => 'Failed to remove order',
            ];

        }

    } else {

        $output = [
            'status' => false,
            'message' => 'No matching order found',
        ];

    }


    return response()->json($output);
  }



  public function searchOrder(Request $request , $pharmacy_id) {

    $order = Order::with('pharmacy')->with(['prescription.prescriptionMedications.medication'])->with(['prescription.card.patient.user'])->with(['prescription.card.doctor.user'])->whereHas('prescription.card.doctor.user' , function($query) use ($request) {
        $query->where('name' , 'like' , '%' . $request->name . '%');
    })->where('pharmacy_id', $pharmacy_id)->get();


  if($order) {

    $output = [
        'status' => true,
        'message' => 'Orders retrieved successfully',
        'orders' => $order,

    ];

  } else {

    $output = [
        'status' => false,
        'message' => 'No order found',
    ];

  }

   return response()->json($output);

}


public function searchPatientOrder(Request $request , $patient_id) {

    $order = Order::with('pharmacy')->with(['prescription.prescriptionMedications.medication'])->with(['prescription.card.patient.user'])->with(['prescription.card.doctor.user'])->whereHas('pharmacy' , function($query) use ($request) {
        $query->where('pharmacy_name' , 'like' , '%' . $request->pharmacy_name . '%');})->whereHas('prescription.card.patient.user' , function($query) use ($patient_id) {
        $query->where('patient_id' , $patient_id);
    })->get();


  if($order) {

    $output = [
        'status' => true,
        'message' => 'Orders retrieved successfully',
        'orders' => $order,

    ];

  } else {

    $output = [
        'status' => false,
        'message' => 'No matching order found',
    ];

  }

   return response()->json($output);

}

}
