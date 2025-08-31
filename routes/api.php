<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PharmacistController;
use App\Http\Controllers\PatientController;
use App\http\Controllers\MedicationController;
use App\http\Controllers\StockController;
use App\http\Controllers\UserClaimController;
use App\http\Controllers\PatientClaimController;
use App\http\Controllers\PrescriptionController;
use App\Http\Controllers\PrescriptionMedicationController;
use App\Http\Controllers\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/test' , function() {

   return 'test ---> Success';

});


Route::get('/' , function(){
    return View('welcome');
});



// ----------------------------------------------------------------------- //
Route::group(['prefix' => 'admin'],function () {

    Route::get('/profile-admin/{user_id}' , [AdminController::class , 'getProfileAdmin'])->middleware('auth:sanctum');
    Route::put('/update-profile/{user_id}' , [AdminController::class , 'updateProfileAdmin'])->middleware('auth:sanctum');
    Route::put('/update-profile-with-image/{user_id}' , [AdminController::class , 'updateProfileAdminWithImage'])->middleware('auth:sanctum');
    Route::post('/add-account' , [AdminController::class , 'addAccount'])->middleware('auth:sanctum');
    Route::get('/all-users' , [AdminController::class , 'getUsers'])->middleware('auth:sanctum');
    Route::get('/all-admins' , [AdminController::class , 'getAdmins'])->middleware('auth:sanctum');
    Route::get('/all-doctors' , [AdminController::class , 'getDoctors'])->middleware('auth:sanctum');
    Route::get('/all-pharmacists' , [AdminController::class , 'getPharmacists'])->middleware('auth:sanctum');
    Route::get('/all-patients' , [AdminController::class , 'getPatients'])->middleware('auth:sanctum');
    Route::get('/get-user-info/{user_id}' , [AdminController::class , 'getUserInfo'])->middleware('auth:sanctum');
    Route::post('/search-user' , [AdminController::class , 'searchUser'])->middleware('auth:sanctum');
    Route::post('/search-admin' , [AdminController::class , 'searchAdmin'])->middleware('auth:sanctum');
    Route::post('/search-doctor' , [AdminController::class , 'searchDoctor'])->middleware('auth:sanctum');
    Route::post('/search-patient' , [AdminController::class , 'searchPatient'])->middleware('auth:sanctum');
    Route::post('/search-pharmacist' , [AdminController::class , 'searchPharmacist'])->middleware('auth:sanctum');
    Route::delete('/delete-user/{user_id}' , [AdminController::class , 'deleteUser']);
    Route::get('/get-user-claims' , [UserClaimController::class , 'getUserClaims'])->middleware('auth:sanctum');
    Route::post('/change-status-user-claim' , [UserClaimController::class , 'changeStatusUserClaim'])->middleware('auth:sanctum');
    Route::delete('/delete-user-claim' , [UserClaimController::class , 'deleteUserClaims'])->middleware('auth:sanctum');
});





// user authentification

Route::group(['prefix' => 'user'], function () {

    Route::post('/register' , [UserController::class , 'register']);
    Route::post('/login' , [UserController::class , 'login']);
    Route::get('/profile' , [UserController::class , 'profile'])->middleware('auth:sanctum');
    Route::put('/update-code-auth/{user_id}' , [UserController::class , 'updateCodeAuth'])->middleware('auth:sanctum');
    Route::put('/change-password/{user_id}' , [UserController::class , 'changePassword'])->middleware('auth:sanctum');
    Route::post('/logout' , [UserController::class , 'logout'])->middleware('auth:sanctum');
    Route::post('/add-claim' , [UserClaimController::class , 'addClaim'])->middleware('auth:sanctum');
    Route::post('/check-email' , [UserController::class , 'checkEmail']);
    Route::put('/reset-password/{email}' , [UserController::class , 'resetPassword']);
    Route::delete('/delete-account' , [UserController::class , 'deleteAccount'])->middleware('auth:sanctum');
    Route::post('/check-account' , [UserController::class , 'checkAccount']);



});


// Route::group(['prefix' => 'admin'] , function () {

//     Route::delete('/delete-user' , [UserController::class , 'deleteUser']);




// });




// doctor

Route::group(['prefix' => 'doctor'] , function() {

    Route::post('/complete-register' , [DoctorController::class , 'completeRegisterDoc'])->middleware('auth:sanctum');
    Route::get('/profile-doctor/{user_id}' , [DoctorController::class , 'getProfileDoctor'])->middleware('auth:sanctum');

    Route::post('/add-patient' , [DoctorController::class , 'addPatientAccount'])->middleware('auth:sanctum');
    Route::get('/all-patients' , [DoctorController::class , 'getPatients'])->middleware('auth:sanctum');
    Route::post('/search-patient' , [DoctorController::class , 'searchPatient'])->middleware('auth:sanctum');


    Route::put('/update-profile/{user_id}' , [DoctorController::class , 'updateProfileDoctor'])->middleware('auth:sanctum');
    Route::put('/update-profile-with-image/{user_id}' , [DoctorController::class , 'updateProfileDoctorWithImage'])->middleware('auth:sanctum');


    Route::post('/add-card' , [DoctorController::class , 'addCard'])->middleware('auth:sanctum');
    Route::get('/card/{doctor_id}/patient/{patient_id}' , [DoctorController::class , 'getCard'])->middleware('auth:sanctum');
    Route::put('/update-card' , [DoctorController::class , 'updateCard'])->middleware('auth:sanctum');
    Route::delete('/delete-card' , [DoctorController::class , 'deleteCard'])->middleware('auth:sanctum');


    Route::get('/all-prescriptions/{card_id}' , [PrescriptionController::class , 'getPrescriptions'])->middleware('auth:sanctum');
    Route::post('/add-prescription' , [PrescriptionController::class , 'addPrescription'])->middleware('auth:sanctum');
    Route::post('/add-prescription-medication' , [PrescriptionMedicationController::class , 'addMedicationInPrescription'])->middleware('auth:sanctum');

    Route::delete('/remove-medication-prescription/{prescription_medication_id}' , [PrescriptionMedicationController::class , 'removeMedicationFromPrescription'])->middleware('auth:sanctum');


    Route::put('/edit-prescription/{prescription_id}' , [PrescriptionController::class , 'editPrescription'])->middleware('auth:sanctum');
    Route::put('/edit-medications-prescription/{prescription_medication_id}' , [PrescriptionMedicationController::class , 'editMedicationPrescription'])->middleware('auth:sanctum');
    Route::post('/check-exist-medication-prescription' , [PrescriptionMedicationController::class , 'checkExistMedicationInPrescription'])->middleware('auth:sanctum');

    Route::delete('/remove-prescription/{prescription_id}' , [PrescriptionController::class , 'removePrescription'])->middleware('auth:sanctum');


    Route::get('/all-patient-claims/{doctor_id}' , [PatientClaimController::class , 'getPatientClaims'])->middleware('auth:sanctum');
    Route::post('/change-status-claim' , [PatientClaimController::class , 'changeStatusClaim'])->middleware('auth:sanctum');




    Route::get('/all-pharmacies' , [DoctorController::class , 'getPharmacies'])->middleware('auth:sanctum');
    Route::post('/search-pharmacy' , [DoctorController::class , 'searchPharmacy'])->middleware('auth:sanctum');
    Route::post('/stock/all-medications' , [DoctorController::class , 'getAllMedicationsFromStockPharmacy'])->middleware('auth:sanctum');
    Route::post('/stock/search-medication' , [DoctorController::class , 'searchMedicationInStockPharmacy'])->middleware('auth:sanctum');

    Route::get('/get-pharmacies/{medications}/quantity/{quantities}' , [StockController::class , 'getPharmaciesHaveMedicationsPrescription'])->middleware('auth:sanctum');
    Route::post('/send-order-to-pharmacy' , [OrderController::class , 'sendOrderToPharmacy'])->middleware('auth:sanctum');
    Route::delete('/remove-order/{prescription_id}' , [OrderController::class , 'removeOrder'])->middleware('auth:sanctum');

    Route::delete('/remove-claim' , [PatientClaimController::class , 'deleteClaim'])->middleware('auth:sanctum');




});






// pharmacist

Route::group(['prefix' => 'pharmacist'] , function () {

    Route::post('/check-pharmacy-name' , [PharmacistController::class , 'checkPharmacyName'])->middleware('auth:sanctum');
    Route::post('/check-exist-pharmacy' , [PharmacistController::class , 'checkExistPharmacy'])->middleware('auth:sanctum');
    Route::post('/complete-register-simple' , [PharmacistController::class , 'completeRegisterSimple'])->middleware('auth:sanctum');

    Route::post('/complete-register' , [PharmacistController::class , 'completeRegisterPharm'])->middleware('auth:sanctum');
    Route::get('/profile-pharmacist/{user_id}' , [PharmacistController::class , 'getProfilePharmacist'])->middleware('auth:sanctum');
    Route::get('/pharmacy/{pharmacy_id}' , [PharmacistController::class , 'getPharmacy'])->middleware('auth:sanctum');



    Route::put('/update-profile/{user_id}/pharmacy/{pharmacy_id}' , [PharmacistController::class , 'updateProfilePharmacist'])->middleware('auth:sanctum');
    Route::put('/update-profile-with-image/{user_id}/pharmacy/{pharmacy_id}' , [PharmacistController::class , 'updateProfilePharmacistWithImage'])->middleware('auth:sanctum');


    Route::post('/check-exist-medication' , [MedicationController::class , 'checkExistMedication'])->middleware('auth:sanctum');
    Route::post('/add-medication' , [MedicationController::class , 'addMedication'])->middleware('auth:sanctum');
    Route::get('/all-medications' , [MedicationController::class , 'getAllMedications'])->middleware('auth:sanctum');
    Route::put('/edit-medication' , [MedicationController::class , 'editMedication'])->middleware('auth:sanctum');
    Route::post('/search-medication' , [MedicationController::class , 'searchMedication'])->middleware('auth:sanctum');


    Route::post('/stock/add-medication' , [StockController::class , 'addMedicationInStock'])->middleware('auth:sanctum');
    Route::post('/stock/search-medication' , [StockController::class , 'searchMedicationInStock'])->middleware('auth:sanctum');
    Route::get('/stock/all-medications/{pharmacy_id}' , [StockController::class , 'getAllMedicationsFromStock'])->middleware('auth:sanctum');
    Route::put('/stock/edit-medication' , [StockController::class , 'editMedicationStock'])->middleware('auth:sanctum');
    Route::put('/stock/edit-medication-with-image' , [StockController::class , 'editMedicationStockWithImage'])->middleware('auth:sanctum');
    Route::delete('/stock/remove-medication' , [StockController::class , 'removeMedicationStock'])->middleware('auth:sanctum');
    Route::post('/stock/check-exist-medication' , [StockController::class , 'checkExistMedicationInStock'])->middleware('auth:sanctum');

    Route::get('/get-orders/{pharmacy_id}' , [OrderController::class , 'getOrders'])->middleware('auth:sanctum');
    Route::post('/accept-order' , [OrderController::class , 'acceptOrder'])->middleware('auth:sanctum');
    Route::post('/refuse-order' , [OrderController::class , 'refuseOrder'])->middleware('auth:sanctum');
    Route::post('/search-order/{pharmacy_id}' , [OrderController::class , 'searchOrder'])->middleware('auth:sanctum');

    Route::get('/stock/change-quantity-medication/{medications}/quantity/{quantities}/pharmacy/{pharmacy_id}' , [StockController::class , 'decrementQuantityMedications'])->middleware('auth:sanctum');


});






// patient

Route::group(['prefix' => 'patient'] , function () {

    Route::get('/profile-patient/{user_id}' , [PatientController::class , 'getProfilePatient'])->middleware('auth:sanctum');
    Route::get('/all-cards/{patient_id}' , [PatientController::class , 'getAllCards'])->middleware('auth:sanctum');
    Route::post('/search-card/{patient_id}' , [PatientController::class , 'searchCard'])->middleware('auth:sanctum');

    Route::get('/all-doctors' , [PatientController::class , 'getAllDoctors'])->middleware('auth:sanctum');
    Route::post('/search-doctor' , [PatientController::class , 'searchDoctor'])->middleware('auth:sanctum');

    Route::put('/update-profile/{user_id}' , [PatientController::class , 'updateProfilePatient'])->middleware('auth:sanctum');
    Route::put('/update-profile-with-image/{user_id}' , [PatientController::class , 'updateProfilePatientWithImage'])->middleware('auth:sanctum');

    Route::post('/add-claim-to-doctor' , [PatientClaimController::class , 'addClaimToDoctor'])->middleware('auth:sanctum');

    Route::get('/all-prescriptions/{card_id}' , [PrescriptionController::class , 'getPrescriptions'])->middleware('auth:sanctum');

    Route::get('/orders/{patient_id}' , [OrderController::class , 'getPatientOrders'])->middleware('auth:sanctum');

    Route::post('/search-order/{patient_id}' , [OrderController::class , 'searchPatientOrder'])->middleware('auth:sanctum');



});




// ----------------------------------------------------------------------- //

