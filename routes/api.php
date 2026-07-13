<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Auth\MeController;
use App\Http\Controllers\Api\v1\SettingController;
use App\Http\Controllers\Api\v1\VisitorController;
use App\Http\Controllers\Api\v1\EmployeeController;
use App\Http\Controllers\Api\v1\LanguageController;
use App\Http\Controllers\Api\v1\DashboardController;
use App\Http\Controllers\Api\v1\AttendanceController;
use App\Http\Controllers\Api\v1\Auth\LoginController;
use App\Http\Controllers\Api\v1\Auth\LogoutController;
use App\Http\Controllers\Api\v1\PreRegisterController;
use App\Http\Controllers\Api\v1\Auth\RegisterController;
use App\Http\Controllers\Api\v1\PushNotificationController;

// ============================================
// 🚀 NEW CONTROLLER IMPORTS FOR MULTI-TENANT SYSTEM
// ============================================
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SubUnitController;
use App\Http\Controllers\ResidentProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InvitationController;

// ============================================
// 📱 PWA API CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\PwaApiController;

// ============================================
// 🔑 OTP & ACCESS DEVICE CONTROLLER IMPORTS
// ============================================
use App\Http\Controllers\AccessOTPController;
use App\Http\Controllers\AccessDeviceController;

// ============================================
// 📊 ANALYTICS CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\AnalyticsController;

Route::group(['prefix' => 'v1'], function () {

    Route::post('login', [LoginController::class, 'action']); //done

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [LogoutController::class, 'action']); //done
        Route::get('me', [MeController::class, 'action']); //done
        Route::get('dashboard', [DashboardController::class, 'index']); //done
        Route::post('profile-update', [MeController::class, 'update']); //done
        Route::post('change-password', [MeController::class, 'changePassword']); //done
        Route::get('refresh-token', [MeController::class, 'refresh']); //done
    });

    //Emloyee
    Route::get('employee', [EmployeeController::class, 'index']); //done
    Route::get('employee/{id}/show', [EmployeeController::class, 'show']); //done


    //Pre-Register
    Route::get('preregister/', [PreRegisterController::class, 'index']); //done
    Route::post('preregister', [PreRegisterController::class, 'store']); //done
    Route::get('preregister/{id}/show', [PreRegisterController::class, 'show']); //done
    Route::post('preregister/{id}/', [PreRegisterController::class, 'update']); //done
    Route::delete('preregister/{id}', [PreRegisterController::class, 'destroy']); //done
    Route::post('preregister/check-preregister/find', [PreRegisterController::class, 'checkPreRegister']); //done
    Route::get('preregister/search/{keyWord}', [PreRegisterController::class, 'search']);


    //attendance
    Route::get('attendance/{date?}', [AttendanceController::class, 'index']);
    Route::get('attendance/user/status', [AttendanceController::class, 'getStatus']);
    Route::post('attendance/user/clock-in', [AttendanceController::class, 'clockIn']);
    Route::get('attendance/user/clock-out', [AttendanceController::class, 'clockOut']);

    //visitor
    Route::get('visitors/', [VisitorController::class, 'index']);
    Route::get('visitors/search/{keyWord}', [VisitorController::class, 'search']);
    Route::get('visitors/show/{id}', [VisitorController::class, 'show']);
    Route::post('visitors/add', [VisitorController::class, 'store']);
    Route::post('visitors/edit/{id}', [VisitorController::class, 'update']);
    Route::delete('visitors/delete/{id}', [VisitorController::class, 'destroy']);
    Route::get('visitor/check-out/{id}', [VisitorController::class, 'checkout']);
    Route::post('visitor/check-in', [VisitorController::class, 'checkin']);
    Route::post('visitor/check-in/validator', [VisitorController::class, 'checkinCheck']);
    Route::get('visitor/change-status/{id}/{status}',  [VisitorController::class, 'changeStatus']);
    Route::post('visitor/find_visitor/', [VisitorController::class, 'findVisitor']); //done

    Route::get('settings/', [SettingController::class, 'index']);
    Route::get('languages/', [LanguageController::class, 'index']);

    //push notification
    Route::post('fcm-subscribe', [PushNotificationController::class, 'fcmSubscribe']);
    Route::post('fcm-unsubscribe', [PushNotificationController::class, 'fcmUnsubscribe']);

    // ============================================
    // 🏢 MULTI-TENANT / FACILITY MANAGEMENT ROUTES
    // ============================================
    Route::apiResource('organizations', OrganizationController::class);
    Route::apiResource('facilities', FacilityController::class);
    Route::apiResource('people', PersonController::class);
    Route::get('people/by-type', [PersonController::class, 'getByType'])->name('people.by-type');

    // ============================================
    // 🏠 RESIDENTIAL MODULE ROUTES
    // ============================================
    // SubUnits (Blocks, Floors, Apartments, Streets)
    Route::apiResource('sub-units', SubUnitController::class);
    Route::get('sub-units/tree/{facilityId}', [SubUnitController::class, 'getTree']);

    // Resident Profiles
    Route::apiResource('resident-profiles', ResidentProfileController::class);
    Route::get('resident-profiles/by-sub-unit/{subUnitId}', [ResidentProfileController::class, 'getBySubUnit']);

    // ============================================
    // 🚗 VEHICLE MANAGEMENT ROUTES
    // ============================================
    Route::apiResource('vehicles', VehicleController::class);
    Route::get('vehicles/by-owner/{ownerType}/{ownerId}', [VehicleController::class, 'getByOwner']);
    Route::get('vehicles/search/{facilityId}/{query}', [VehicleController::class, 'search']);

    // ============================================
    // 📦 DELIVERY MANAGEMENT ROUTES
    // ============================================
    Route::apiResource('deliveries', DeliveryController::class);
    Route::post('deliveries/{id}/mark-received', [DeliveryController::class, 'markReceived']);
    Route::get('deliveries/by-recipient/{personId}', [DeliveryController::class, 'getByRecipient']);
    Route::get('deliveries/stats/{facilityId}', [DeliveryController::class, 'getStats']);

    // ============================================
    // 📨 INVITATION MANAGEMENT ROUTES
    // ============================================
    Route::apiResource('invitations', InvitationController::class);
    Route::get('invitations/verify/{qrCode}', [InvitationController::class, 'verify']);
    Route::post('invitations/check-in', [InvitationController::class, 'checkIn']);
    Route::post('invitations/check-out', [InvitationController::class, 'checkOut']);
    Route::get('invitations/by-host/{personId}', [InvitationController::class, 'getByHost']);
    Route::get('invitations/stats/{facilityId}', [InvitationController::class, 'getStats']);
});


// ============================================
// 📱 PWA API ROUTES (No v1 prefix)
// ============================================
Route::group(['prefix' => 'api/pwa', 'middleware' => 'auth:api'], function () {
    Route::get('/stats', [PwaApiController::class, 'stats']);
    Route::get('/recent-visitors', [PwaApiController::class, 'recentVisitors']);
    Route::get('/employee-stats', [PwaApiController::class, 'employeeStats']);
    Route::get('/pending-approvals', [PwaApiController::class, 'pendingApprovals']);
    Route::post('/approve/{id}', [PwaApiController::class, 'approveVisitor']);
    Route::post('/reject/{id}', [PwaApiController::class, 'rejectVisitor']);
    Route::get('/resident-stats', [PwaApiController::class, 'residentStats']);
    Route::get('/pending-guest-approvals', [PwaApiController::class, 'pendingGuestApprovals']);
    Route::post('/generate-otp', [PwaApiController::class, 'generateOTP']);
    Route::get('/visitors', [PwaApiController::class, 'visitors']);
    Route::get('/history', [PwaApiController::class, 'history']);
    Route::post('/checkin', [PwaApiController::class, 'checkin']);

    // ============================================
    // 📱 PUSH NOTIFICATION API ROUTES
    // ============================================
    Route::post('/push/subscribe', [PushNotificationController::class, 'subscribe']);
    Route::post('/push/unsubscribe', [PushNotificationController::class, 'unsubscribe']);
    Route::post('/push/test', [PushNotificationController::class, 'test']);
    Route::get('/push/subscriptions', [PushNotificationController::class, 'getUserSubscriptions']);
    Route::get('/vapid-key', [PushNotificationController::class, 'getVapidKey']);
});

// ============================================
// 🔑 OTP & ACCESS CONTROL ROUTES
// ============================================
Route::group(['prefix' => 'api/otp', 'middleware' => 'auth:api'], function () {
    Route::post('/generate', [AccessOTPController::class, 'generate']);
    Route::post('/validate', [AccessOTPController::class, 'validateOtp']);
    Route::post('/check', [AccessOTPController::class, 'check']);
    Route::get('/history', [AccessOTPController::class, 'history']);
    Route::post('/send-whatsapp', [AccessOTPController::class, 'sendViaWhatsApp']);
});

// ============================================
// 📟 ACCESS DEVICE ROUTES
// ============================================
Route::group(['prefix' => 'api/devices', 'middleware' => 'auth:api'], function () {
    Route::get('/', [AccessDeviceController::class, 'index']);
    Route::post('/', [AccessDeviceController::class, 'store']);
    Route::get('/{accessDevice}', [AccessDeviceController::class, 'show']);
    Route::put('/{accessDevice}', [AccessDeviceController::class, 'update']);
    Route::delete('/{accessDevice}', [AccessDeviceController::class, 'destroy']);
    Route::post('/{accessDevice}/test', [AccessDeviceController::class, 'testConnection']);
});

// ============================================
// 📊 ANALYTICS API ROUTES
// ============================================
Route::group(['prefix' => 'api/analytics', 'middleware' => 'auth:api'], function () {
    Route::get('/stats', [AnalyticsController::class, 'getStats']);
    Route::get('/trends', [AnalyticsController::class, 'getTrends']);
    Route::get('/peak-hours', [AnalyticsController::class, 'getPeakHours']);
    Route::get('/top-hosts', [AnalyticsController::class, 'getTopHosts']);
    Route::get('/status-distribution', [AnalyticsController::class, 'getStatusDistribution']);
    Route::get('/breakdown', [AnalyticsController::class, 'getBreakdown']);
    Route::get('/daily-activity', [AnalyticsController::class, 'getDailyActivity']);
    Route::get('/facility-comparison', [AnalyticsController::class, 'getFacilityComparison']);
    Route::get('/facility-type-breakdown', [AnalyticsController::class, 'getFacilityTypeBreakdown']);
});