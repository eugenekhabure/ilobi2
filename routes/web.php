<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\EnvironmentController;
use App\Http\Controllers\PurchaseCodeController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DepartmentsController;
use App\Http\Controllers\Admin\PreRegisterController;
use App\Http\Controllers\Admin\DesignationsController;
use App\Http\Controllers\Admin\LocalizationController;
use App\Http\Controllers\Admin\VisitorReportController;
use App\Http\Controllers\Admin\EmployeeReportController;
use App\Http\Controllers\Admin\WebNotificationController;
use App\Http\Controllers\Admin\AttendanceReportController;
use App\Http\Controllers\Admin\PreRegistersReportController;
use App\Http\Controllers\Admin\SubscriptionController;

// ============================================
// 🚀 NEW ONBOARDING CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\OnboardingController;

// ============================================
// 🏠 RESIDENTIAL MODULE CONTROLLER IMPORTS
// ============================================
use App\Http\Controllers\SubUnitController;
use App\Http\Controllers\ResidentProfileController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\InvitationController;

// ============================================
// 📱 PWA CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\PwaController;

// ============================================
// 📟 ACCESS DEVICE CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\AccessDeviceController;

// ============================================
// 📊 ANALYTICS CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\AnalyticsController;

// ============================================
// 📝 SELF-SERVICE CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\SelfServiceController;

// ============================================
// 🔐 TWO-FACTOR AUTH CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\Auth\TwoFactorController;

// ============================================
// 🚨 EMERGENCY ALERT CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\EmergencyAlertController;

// ============================================
// 📢 BROADCAST CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\BroadcastController;

// ============================================
// 📢 ANNOUNCEMENT CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\AnnouncementController;

// ============================================
// 📝 FEEDBACK CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\FeedbackController;

// ============================================
// 🔧 MAINTENANCE REQUEST CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\MaintenanceRequestController;

// ============================================
// 🔧 MAINTENANCE CATEGORY CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\MaintenanceCategoryController;

// ============================================
// 🏊 AMENITY CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\AmenityController;

// ============================================
// 📢 COMMUNITY CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\CommunityController;

// ============================================
// 👤 STAFF CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\StaffController;

// ============================================
// 🏢 STAFF DEPARTMENT CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\StaffDepartmentController;

// ============================================
// 🔐 ZKTECO CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\ZKTecoController;

// ============================================
// 📹 HIKVISION CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\HikvisionController;

// ============================================
// 📅 GOOGLE CALENDAR CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\GoogleCalendarController;

// ============================================
// 🚫 PHASE 10: BLACKLIST CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\Admin\BlacklistController;

// ============================================
// 👁️ PHASE 10: WATCHLIST CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\Admin\WatchlistController;

// ============================================
// ⚠️ PHASE 10: ANOMALY ALERT CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\Admin\AnomalyAlertController;

// ============================================
// 👤 PHASE 10: FACIAL RECOGNITION CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\Admin\FacialRecognitionLogController;

// ============================================
// 📹 PHASE 10: SURVEILLANCE CONTROLLER IMPORT
// ============================================
use App\Http\Controllers\Admin\SurveillanceFeedController;

Auth::routes();

Route::group(['middleware' => ['installed']], function () {
    Auth::routes(['verify' => false]);
});

// ============================================
// 🚀 LANDING PAGE (Public - No Auth Required)
// ============================================
Route::get('/', function () {
    return view('frontend.index');
})->name('home');

// ============================================
// 🚀 ONBOARDING WIZARD ROUTES
// ============================================
Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'middleware' => ['web', 'install']], function () {
    Route::post('environment/saveWizard', [EnvironmentController::class, 'saveWizard'])->name('environmentSaveWizard');

    Route::get('purchase-code', [PurchaseCodeController::class, 'index'])->name('purchase_code');

    Route::post('purchase-code', [PurchaseCodeController::class, 'action'])->name('purchase_code.check');
});

// ============================================
// 🚀 ADMIN LOGIN
// ============================================
Route::group(['prefix' => 'admin', 'middleware' => ['installed'], 'namespace' => 'Admin', 'as' => 'admin.'], function () {
    Route::get('login', [LoginController::class, 'showLoginForm']);
});

Route::get('admin/lang/{locale}', [LocalizationController::class, 'index'])->middleware(['installed'])->name('admin.lang.index');

// ============================================
// 🚀 ADMIN AUTHENTICATED ROUTES
// ============================================
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'installed'], 'as' => 'admin.'], function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('profile/update/{profile}', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/change', [ProfileController::class, 'change'])->name('profile.change');
    Route::resource('adminusers', AdminUserController::class);
    Route::get('get-adminusers', [AdminUserController::class, 'getAdminUsers'])->name('adminusers.get-adminusers');
    Route::resource('role', RoleController::class);
    Route::post('role/save-permission/{id}', [RoleController::class, 'savePermission'])->name('role.save-permission');

    //designations
    Route::resource('designations', DesignationsController::class);
    Route::get('get-designations', [DesignationsController::class, 'getDesignations'])->name('designations.get-designations');

    //departments
    Route::resource('departments', DepartmentsController::class);
    Route::get('get-departments', [DepartmentsController::class, 'getDepartments'])->name('departments.get-departments');

    //web-token
    Route::post('store-token', [WebNotificationController::class, 'store'])->name('store.token');

    //employee route
    Route::resource('employees', EmployeeController::class);
    Route::get('get-employees', [EmployeeController::class, 'getEmployees'])->name('employees.get-employees');
    Route::get('employees/get-pre-registers/{id}', [EmployeeController::class, 'getPreRegister'])->name('employees.get-pre-registers');
    Route::get('employees/get-visitors/{id}', [EmployeeController::class, 'getVisitor'])->name('employees.get-visitors');
    Route::put('employees/check/{id}', [EmployeeController::class, 'checkEmployee'])->name('employees.check');

    //pre-registers
    Route::resource('pre-registers', PreRegisterController::class);
    Route::get('get-pre-registers', [PreRegisterController::class, 'getPreRegister'])->name('pre-registers.get-pre-registers');

    //visitors
    Route::resource('visitors', VisitorController::class);
    Route::post('visitor/search', [VisitorController::class, 'search'])->name('visitor.search');
    Route::get('visitor/check-out/{visitingDetail}', [VisitorController::class, 'checkout'])->name('visitors.checkout');
    Route::get('visitor/change-status/{id}/{status}/{dashboard}',  [VisitorController::class, 'changeStatus'])->name('visitor.change-status');
    Route::get('get-visitors', [VisitorController::class, 'getVisitor'])->name('visitors.get-visitors');
    Route::get('visitor/disable/{id}',  [VisitorController::class, 'visitorDisable'])->name('visitors.disable');

    //report
    Route::get('admin-visitor-report', [VisitorReportController::class, 'index'])->name('admin-visitor-report.index');
    Route::post('admin-visitor-report', [VisitorReportController::class, 'index'])->name('admin-visitor-report.post');

    Route::get('admin-pre-registers-report', [PreRegistersReportController::class, 'index'])->name('admin-pre-registers-report.index');
    Route::post('admin-pre-registers-report', [PreRegistersReportController::class, 'index'])->name('admin-pre-registers-report.post');

    Route::get('attendance-report', [AttendanceReportController::class, 'index'])->name('attendance-report.index');
    Route::post('attendance-report', [AttendanceReportController::class, 'index'])->name('attendance-report.post');

    Route::get('employee-report', [EmployeeReportController::class, 'index'])->name('employee-report.index');
    Route::post('employee-report', [EmployeeReportController::class, 'index'])->name('employee-report.post');


    Route::post('admin-attendance/clockin', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
    Route::post('admin-attendance/clockout', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');

    Route::resource('attendance', AttendanceController::class);
    Route::get('get-attendance', [AttendanceController::class, 'getAttendance'])->name('attendance.get-attendance');
    //language
    Route::resource('language', LanguageController::class);
    Route::get('get-language', [LanguageController::class, 'getLanguage'])->name('language.get-language');
    Route::get('language/change-status/{id}/{status}', [LanguageController::class, 'changeStatus'])->name('language.change-status');

    //Addons
    Route::resource('addons', AddonController::class);
    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'siteSettingUpdate'])->name('site-update');
        Route::get('sms', [SettingController::class, 'smsSetting'])->name('sms');
        Route::post('sms', [SettingController::class, 'smsSettingUpdate'])->name('sms-update');
        Route::get('fcm-notification', [SettingController::class, 'fcmSetting'])->name('fcm');
        Route::post('fcm-notification', [SettingController::class, 'fcmSettingUpdate'])->name('fcm-update');
        Route::get('email', [SettingController::class, 'emailSetting'])->name('email');
        Route::post('email', [SettingController::class, 'emailSettingUpdate'])->name('email-update');
        Route::get('notification', [SettingController::class, 'notificationSetting'])->name('notification');
        Route::post('notification', [SettingController::class, 'notificationSettingUpdate'])->name('notification-update');
        Route::get('emailtemplate', [SettingController::class, 'emailTemplateSetting'])->name('email-template');
        Route::post('emailtemplate', [SettingController::class, 'mailTemplateSettingUpdate'])->name('email-template-update');
        Route::get('homepage', [SettingController::class, 'homepageSetting'])->name('homepage');
        Route::post('homepage', [SettingController::class, 'homepageSettingUpdate'])->name('homepage-update');
        Route::get('whatsapp', [SettingController::class, 'whatsappSetting'])->name('whatsapp-message');
        Route::post('whatsapp', [SettingController::class, 'whatsappSettingupdate'])->name('whatsapp-message-update');
    });

    // ============================================
    // 🏠 RESIDENTIAL MODULE WEB ROUTES
    // ============================================
    Route::resource('sub-units', SubUnitController::class);
    Route::resource('resident-profiles', ResidentProfileController::class);
    Route::resource('people', PersonController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('deliveries', DeliveryController::class);
    Route::resource('invitations', InvitationController::class);

    // ============================================
    // 📟 ACCESS DEVICES WEB ROUTES
    // ============================================
    Route::resource('access-devices', AccessDeviceController::class);

    // ============================================
    // 📊 ANALYTICS ROUTES
    // ============================================
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // ============================================
    // 🚨 EMERGENCY ALERT ROUTES
    // ============================================
    Route::resource('emergency-alerts', EmergencyAlertController::class);

    // ============================================
    // 📢 BROADCAST ROUTES
    // ============================================
    Route::resource('broadcasts', BroadcastController::class);
    Route::get('broadcasts/template/{id}', [BroadcastController::class, 'useTemplate'])->name('broadcasts.template');

    // ============================================
    // 📢 ANNOUNCEMENT ROUTES
    // ============================================
    Route::resource('announcements', AnnouncementController::class);
    Route::get('announcements/toggle-pin/{id}', [AnnouncementController::class, 'togglePin'])->name('announcements.toggle-pin');
    Route::get('announcements/toggle-active/{id}', [AnnouncementController::class, 'toggleActive'])->name('announcements.toggle-active');

    // ============================================
    // 📝 FEEDBACK ROUTES
    // ============================================
    Route::resource('feedback', FeedbackController::class)->except(['create', 'edit', 'update']);
    Route::post('feedback/toggle-flag/{id}', [FeedbackController::class, 'toggleFlag'])->name('feedback.toggle-flag');

    // ============================================
    // 🔧 MAINTENANCE REQUEST ROUTES
    // ============================================
    Route::resource('maintenance', MaintenanceRequestController::class);
    Route::post('maintenance/comment/{id}', [MaintenanceRequestController::class, 'addComment'])->name('maintenance.comment');

    // ============================================
    // 🔧 MAINTENANCE CATEGORY ROUTES
    // ============================================
    Route::resource('maintenance-categories', MaintenanceCategoryController::class);
    Route::get('maintenance-categories/toggle-status/{id}', [MaintenanceCategoryController::class, 'toggleStatus'])->name('maintenance-categories.toggle-status');

    // ============================================
    // 🏊 AMENITY ROUTES
    // ============================================
    Route::resource('amenities', AmenityController::class);
    Route::get('amenities/toggle-status/{id}', [AmenityController::class, 'toggleStatus'])->name('amenities.toggle-status');
    Route::get('amenities/{id}/bookings', [AmenityController::class, 'bookings'])->name('amenities.bookings');
    Route::get('amenities/bookings/{id}', [AmenityController::class, 'showBooking'])->name('amenities.show-booking');
    Route::put('amenities/bookings/{id}/status', [AmenityController::class, 'updateBookingStatus'])->name('amenities.update-booking-status');

    // ============================================
    // 📢 COMMUNITY ROUTES
    // ============================================
    Route::resource('community', CommunityController::class);
    Route::post('community/comment/{id}', [CommunityController::class, 'addComment'])->name('community.comment');
    Route::post('community/like/{id}', [CommunityController::class, 'likePost'])->name('community.like');
    Route::get('community/toggle-featured/{id}', [CommunityController::class, 'toggleFeatured'])->name('community.toggle-featured');
    Route::post('community/update-status/{id}', [CommunityController::class, 'updateStatus'])->name('community.update-status');
    Route::delete('community/comment/{id}', [CommunityController::class, 'deleteComment'])->name('community.delete-comment');

    // ============================================
    // 📢 COMMUNITY PWA ROUTES
    // ============================================
    Route::get('api/community/posts', [CommunityController::class, 'getPwaPosts'])->name('api.community.posts');
    Route::get('api/community/post/{id}', [CommunityController::class, 'getPwaPost'])->name('api.community.post');

    // ============================================
    // 👤 STAFF ROUTES
    // ============================================
    Route::resource('staff', StaffController::class);
    Route::get('staff/toggle-emergency/{id}', [StaffController::class, 'toggleEmergency'])->name('staff.toggle-emergency');

    // ============================================
    // 👤 STAFF PWA ROUTES
    // ============================================
    Route::get('api/staff', [StaffController::class, 'getPwaStaff'])->name('api.staff');
    Route::get('api/staff/emergency', [StaffController::class, 'getEmergencyContacts'])->name('api.staff.emergency');

    // ============================================
    // 🏢 STAFF DEPARTMENTS ROUTES
    // ============================================
    Route::resource('staff-departments', StaffDepartmentController::class);
    Route::get('staff-departments/toggle-status/{id}', [StaffDepartmentController::class, 'toggleStatus'])->name('staff-departments.toggle-status');

    // ============================================
    // 🔐 ZKTECO ROUTES
    // ============================================
    Route::resource('zkteco', ZKTecoController::class);
    Route::get('zkteco/test/{id}', [ZKTecoController::class, 'testConnection'])->name('zkteco.test');
    Route::post('zkteco/unlock/{id}', [ZKTecoController::class, 'unlockDoor'])->name('zkteco.unlock');

    // ============================================
    // 📹 HIKVISION ROUTES
    // ============================================
    Route::resource('hikvision', HikvisionController::class);
    Route::get('hikvision/test/{id}', [HikvisionController::class, 'testConnection'])->name('hikvision.test');
    Route::post('hikvision/unlock/{id}', [HikvisionController::class, 'unlockDoor'])->name('hikvision.unlock');

    // ============================================
    // 📅 GOOGLE CALENDAR ROUTES
    // ============================================
    Route::get('google-calendar/settings', [GoogleCalendarController::class, 'settings'])->name('google-calendar.settings');
    Route::get('google-calendar/redirect', [GoogleCalendarController::class, 'redirect'])->name('google-calendar.redirect');
    Route::get('google-calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google-calendar.callback');
    Route::post('google-calendar/disconnect', [GoogleCalendarController::class, 'disconnect'])->name('google-calendar.disconnect');
    Route::post('google-calendar/sync', [GoogleCalendarController::class, 'syncPreRegister'])->name('google-calendar.sync');
    Route::get('google-calendar/status', [GoogleCalendarController::class, 'status'])->name('google-calendar.status');

    // ============================================
    // 🚫 PHASE 10: BLACKLIST ROUTES
    // ============================================
    Route::resource('blacklist', BlacklistController::class);
    Route::get('get-blacklist', [BlacklistController::class, 'getBlacklist'])->name('blacklist.get-blacklist');
    Route::put('blacklist/remove/{id}', [BlacklistController::class, 'remove'])->name('blacklist.remove');
    Route::post('blacklist/check', [BlacklistController::class, 'check'])->name('blacklist.check');

    // ============================================
    // 👁️ PHASE 10: WATCHLIST ROUTES
    // ============================================
    Route::resource('watchlist', WatchlistController::class);
    Route::get('get-watchlist', [WatchlistController::class, 'getWatchlist'])->name('watchlist.get-watchlist');
    Route::put('watchlist/resolve/{id}', [WatchlistController::class, 'resolve'])->name('watchlist.resolve');
    Route::get('watchlist/high-priority', [WatchlistController::class, 'getHighPriority'])->name('watchlist.high-priority');

    // ============================================
    // ⚠️ PHASE 10: ANOMALY ALERT ROUTES
    // ============================================
    Route::resource('anomaly-alerts', AnomalyAlertController::class);
    Route::get('get-anomaly-alerts', [AnomalyAlertController::class, 'getAnomalyAlerts'])->name('anomaly-alerts.get-anomaly-alerts');
    Route::put('anomaly-alerts/acknowledge/{id}', [AnomalyAlertController::class, 'acknowledge'])->name('anomaly-alerts.acknowledge');
    Route::put('anomaly-alerts/resolve/{id}', [AnomalyAlertController::class, 'resolve'])->name('anomaly-alerts.resolve');
    Route::put('anomaly-alerts/false-alarm/{id}', [AnomalyAlertController::class, 'falseAlarm'])->name('anomaly-alerts.false-alarm');
    Route::get('anomaly-alerts/stats', [AnomalyAlertController::class, 'getStats'])->name('anomaly-alerts.get-stats');
    Route::get('anomaly-alerts/recent', [AnomalyAlertController::class, 'getRecent'])->name('anomaly-alerts.get-recent');

    // ============================================
    // 👤 PHASE 10: FACIAL RECOGNITION ROUTES
    // ============================================
    Route::resource('facial-recognition', FacialRecognitionLogController::class)->except(['create', 'store', 'edit', 'update']);
    Route::get('get-facial-logs', [FacialRecognitionLogController::class, 'getLogs'])->name('facial-recognition.get-logs');
    Route::delete('facial-recognition/delete-all', [FacialRecognitionLogController::class, 'deleteAll'])->name('facial-recognition.delete-all');
    Route::get('facial-recognition/stats', [FacialRecognitionLogController::class, 'getStats'])->name('facial-recognition.get-stats');
    Route::get('facial-recognition/chart-data', [FacialRecognitionLogController::class, 'getChartData'])->name('facial-recognition.chart-data');
    Route::get('facial-recognition/export', [FacialRecognitionLogController::class, 'export'])->name('facial-recognition.export');

    // ============================================
    // 📹 PHASE 10: SURVEILLANCE ROUTES
    // ============================================
    Route::resource('surveillance', SurveillanceFeedController::class);
    Route::get('get-surveillance-feeds', [SurveillanceFeedController::class, 'getFeeds'])->name('surveillance.get-feeds');
    Route::get('surveillance/stream/{id}', [SurveillanceFeedController::class, 'stream'])->name('surveillance.stream');
    Route::put('surveillance/test-connection/{id}', [SurveillanceFeedController::class, 'testConnection'])->name('surveillance.test-connection');
    Route::put('surveillance/toggle-recording/{id}', [SurveillanceFeedController::class, 'toggleRecording'])->name('surveillance.toggle-recording');
    Route::get('surveillance/stats', [SurveillanceFeedController::class, 'getStats'])->name('surveillance.get-stats');
    Route::get('surveillance/online-cameras', [SurveillanceFeedController::class, 'getOnlineCameras'])->name('surveillance.online-cameras');
    Route::get('surveillance/recording-cameras', [SurveillanceFeedController::class, 'getRecordingCameras'])->name('surveillance.recording-cameras');
});

/*Multi step form*/

Route::group(['middleware' => ['installed']], function () {
    Route::group(['middleware' => ['frontend']], function () {
        Route::get('/home', [CheckInController::class, 'index'])->name('home');
        Route::get('/', [CheckInController::class, 'index'])->name('/');
        Route::get('/scanqr', [CheckInController::class, 'scanQr'])->name('check-in.scan-qr');

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

        Route::post('/checkout', [CheckoutController::class, 'getVisitor'])->name('checkout.index');

        Route::get('/checkout/update/{visitingDetails}', [CheckoutController::class, 'update'])->name('checkout.update');

        Route::get('/check-in', [CheckInController::class, 'index'])->name('check-in');
        Route::get('/check-in/create-step-one', [CheckInController::class, 'createStepOne'])->name('check-in.step-one');
        Route::post('/check-in/create-step-one', [CheckInController::class, 'postCreateStepOne'])->name('check-in.step-one.next');
        Route::get('/check-in/create-step-two', [CheckInController::class, 'createStepTwo'])->name('check-in.step-two');
        Route::post('/check-in/create-step-two', [CheckInController::class, 'store'])->name('check-in.step-two.next');

        Route::get('/check-in/show/{id}', [CheckInController::class, 'show'])->name('check-in.show');
        Route::get('/check-in/return', [CheckInController::class, 'visitor_return'])->name('check-in.return');
        Route::post('/check-in/return', [CheckInController::class, 'find_visitor'])->name('check-in.find.visitor');

        Route::get('/check-in/pre-registered', [CheckInController::class, 'pre_registered'])->name('check-in.pre.registered');
        Route::post('/check-in/pre-registered', [CheckInController::class, 'find_pre_visitor'])->name('check-in.find.pre.visitor');

        /**
         * Scan Qr Code
         */
        Route::get('check-in/visitor-details/{visitorPhone}', [CheckInController::class, 'visitorDetails'])->name('checkin.visitor-details');
        Route::get('check-in/pre-registered/visitor-details/{visitorPhone}', [CheckInController::class, 'preVisitorDetails'])->name('checkin.pre-visitor-details');
    });
});

Route::get('visitor/change-status/{status}/{token}',  [FrontendController::class, 'changeStatus']);

Route::get('qrcode/{number}',  [FrontendController::class, 'qrcode'])->name('qrcode');
Route::get('terms_and_conditions',  [FrontendController::class, 'termsConditions'])->name('terms_and_conditions.view');


// ============================================
// 📦 SUBSCRIPTION ROUTES
// ============================================
Route::get('/admin/packages', [SubscriptionController::class, 'index'])->name('admin.packages.index');
Route::get('/admin/packages/create', function() {
    return view('admin.subscription.create');
})->name('admin.packages.create');
Route::post('/admin/packages/store', [SubscriptionController::class, 'store'])->name('admin.packages.store');
Route::get('/admin/packages/edit/{id}', function($id) {
    $package = DB::table('packages')->where('id', $id)->first();
    return view('admin.subscription.edit', compact('package'));
})->name('admin.packages.edit');
Route::put('/packages/update', [SubscriptionController::class, 'update'])->name('packages.update');
Route::delete('/admin/packages/destroy/{id}', function($id) {
    DB::table('packages')->where('id', $id)->delete();
    return redirect()->route('admin.packages.index')->with('success', 'Package deleted successfully!');
})->name('admin.packages.destroy');
Route::post('/admin/updateStatus', [SubscriptionController::class, 'updateStatus'])->name('admin.package.updateStatus');

Route::get('/admin/purchase', [SubscriptionController::class, 'purchase'])->name('subscription.purchase');
Route::post('/admin/purchase', [SubscriptionController::class, 'purchase_store'])->name('subscription.purchase_store');
Route::get('/admin/purchase_request', [SubscriptionController::class, 'purchase_request'])->name('subscription.requests');


// ============================================
// 📝 SELF-SERVICE PORTAL ROUTES
// ============================================
Route::get('/pre-register', [SelfServiceController::class, 'showForm'])->name('self-service.form');
Route::post('/pre-register', [SelfServiceController::class, 'store'])->name('self-service.store');
Route::get('/pre-register/success/{id}', [SelfServiceController::class, 'success'])->name('self-service.success');

// AJAX endpoints for self-service
Route::get('/api/get-employees', [SelfServiceController::class, 'getEmployees'])->name('self-service.employees');
Route::get('/api/get-residents', [SelfServiceController::class, 'getResidents'])->name('self-service.residents');
Route::get('/api/get-facility-types', [SelfServiceController::class, 'getFacilityTypes'])->name('self-service.facility-types');


// ============================================
// 🔐 TWO-FACTOR AUTHENTICATION ROUTES
// ============================================
Route::get('/2fa/setup', [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
Route::post('/2fa/confirm', [TwoFactorController::class, 'confirmSetup'])->name('2fa.confirm');
Route::get('/2fa/backup-codes', [TwoFactorController::class, 'showBackupCodes'])->name('2fa.backup-codes');
Route::post('/2fa/regenerate', [TwoFactorController::class, 'regenerateBackupCodes'])->name('2fa.regenerate');
Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
Route::get('/2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.post');
Route::get('/2fa/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');


// ============================================
// 🌍 LANGUAGE ROUTES
// ============================================
Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');


// ============================================
// 🚨 EMERGENCY ALERT PUBLIC ROUTE (Acknowledgment)
// ============================================
Route::get('/emergency/acknowledge/{token}', [EmergencyAlertController::class, 'acknowledge'])->name('emergency.acknowledge');


// ============================================
// 📝 FEEDBACK PUBLIC ROUTES
// ============================================
Route::get('/feedback', [FeedbackController::class, 'showForm'])->name('feedback.form');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/feedback/thankyou/{id}', [FeedbackController::class, 'thankYou'])->name('feedback.thankyou');


// ============================================
// 🔐 ZKTECO WEBHOOK ROUTE (Public - Hardware calls this)
// ============================================
Route::post('/api/zkteco/webhook', [ZKTecoController::class, 'webhook'])->name('zkteco.webhook');
Route::post('/api/zkteco/verify', [ZKTecoController::class, 'verifyOtp'])->name('zkteco.verify');

// ============================================
// 📹 HIKVISION WEBHOOK ROUTE (Public - Hardware calls this)
// ============================================
Route::post('/api/hikvision/webhook', [HikvisionController::class, 'webhook'])->name('hikvision.webhook');
Route::post('/api/hikvision/verify', [HikvisionController::class, 'verifyOtp'])->name('hikvision.verify');


// ============================================
// 📱 PWA ROUTES
// ============================================
Route::get('/pwa', [PwaController::class, 'index'])->name('pwa.index');
Route::get('/pwa/login', [PwaController::class, 'login'])->name('pwa.login');
Route::post('/pwa/login', [PwaController::class, 'loginPost'])->name('pwa.login.post');
Route::get('/pwa/dashboard', [PwaController::class, 'dashboard'])->name('pwa.dashboard');
Route::get('/pwa/logout', [PwaController::class, 'logout'])->name('pwa.logout');
Route::get('/pwa/{page}', [PwaController::class, 'loadPage'])->name('pwa.page');

// Service Worker and Manifest
Route::get('/sw.js', function () {
    return response()->file(public_path('sw.js'));
})->name('sw.js');

Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'));
})->name('manifest.json');