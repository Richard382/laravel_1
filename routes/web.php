<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('/getspecialNT', 'HomeController@getspecialNT')->name('getspecialNT');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::group(['middleware' => ['role:admin,client']], function ()
    {
        Route::get('orders/{order}/invoice', 'Orders\DownloadController@index')->name('orders.invoice');
    });
});

Route::get('/', 'HomeController@index')->name("homeindex");
Route::get('/test', function() {
    return view('test');
});
Route::get('/home/getnewinquiries', 'HomeController@getNewInquiries');
Route::get('/home/getnewinquiriestype', 'HomeController@getNewInquiriestype');
Route::get('/test-complain', function(){
    $inquiry = (object)[
        'id'=>122,
        'service' => (object)[
            'type_user' => 'Real estate management and administration'
        ],
        'sum_up' => 'NT valdymas ir administravimas - Žemės sklypai - Prekybos ir paslaugų (K1) - Vilnius - nuo 200.00 eur. iki 300.00 eur.',
        'requirements' => 'test  inquiry detail'
    ];
    $user = \Auth::user();
    $markdown = \Illuminate\Container\Container::getInstance()->make(\Illuminate\Mail\Markdown::class);
//    $html = $markdown->render('emails.contact', compact('firstname', 'url'));
    return $html = $markdown->render("emails.inquiry.inqmail", compact("inquiry","user"));
});

// Inquiries
Route::get('visas-ieskomas-turtas', 'Inquiry\Controller@public')->name('inquiry.public'); // not in use 
Route::get('uzklausos/sukurti', 'Inquiry\RegisterController@showRegistrationForm')->name('inquiry.create');
Route::post('uzklausos', 'Inquiry\RegisterController@store');
Route::post('uzklausos/archive/complain', 'Inquiry\Controller@archiveComplain');

// Companies
Route::post('nt-paslaugu-teikejai/{company}/{inquiry}/susisiekti', 'Company\ContactController@connect');

// Inquiries
Route::get('uzklausos/viesas/{token}', 'Inquiry\Controller@viewPublic')->name('inquiry.show.public');

// Ratings
Route::get('nt-paslaugu-teikejai/{company}/{inquiry}/ivertinti', 'Company\RateController@view')->name('companies.rate');
Route::post('nt-paslaugu-teikejai/{company}/{inquiry}/ivertinti', 'Company\RateController@rate');

// Offers
Route::post('offers/{offer}/{inquiryID}/accept', 'Offer\Controller@accept');

// Companies
Route::get('nt-paslaugu-teikejai', 'Company\Controller@index')->name('companies.index');
Route::get('nt-paslaugu-teikejai/{company}', 'Company\Controller@view')->name('companies.show');
Route::get('/rekomenduojami-partneriai', function(){ return view("pages.recommended-partners"); });

// Authorizes routes
Route::group(['middleware' => ['auth']], function ()
{
    Route::get('mano-uzklausos', 'Inquiry\Controller@my')->name('inquiry.my');

    // Global
    Route::post('notifications/onesignal/{player_id}', 'Notifications\OneSignalController@playerId');
    Route::post('notifications/{notification_id}', 'Notifications\Controller@read');
    Route::post('notifications/view/{notification_id}', 'Notifications\Controller@markview');
    Route::get('notifications/get/new', 'Notifications\Controller@getList');

    Route::get('profilis', 'ProfileController@me')->name('profile.me');
    Route::get('profilis/registruotis-nt-specialistu', 'ProfileController@me')->defaults('becamebroker',true);
    Route::post('profilis', 'ProfileController@update');

    Route::get('uzklausu-archyvas', 'Inquiry\ArchiveController@index')->name('inquiry.archive');
    Route::post('uzklausos/{inquiry}/archyvuoti', 'Inquiry\ArchiveController@archive');
    Route::post('uzklausos/{inquiry}/istrinti-archyva', 'Inquiry\ArchiveController@erase');

    // Only broker routes
    Route::group(['middleware' => ['role:broker']], function ()
    {
        Route::get('uzklausos', 'Inquiry\Controller@index')->name('inquiry.index');
        Route::get('payment/{token}/summary', 'PaymentController@summary')->name('payment.summary');
        Route::get('payment/{payment_type}/{model_id?}', 'PaymentController@view')->name('payment.view');
        Route::post('payment/{payment_type}/{model_id}', 'PaymentController@process');
        Route::get('tapk-matomas', 'BrokerVisibilityController@index')->name('broker.visibility');

        Route::post('offers', 'Offer\RegisterController@store');
    });

    Route::get('uzklausos/{inquiry}', 'Inquiry\Controller@view')->name('inquiry.show');
    Route::post('uzklausos/{inquiry}/delete', 'Inquiry\Controller@deleteInquiry');
});

// Shared login page
Route::get('prisijungti', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('prisijungti', 'Auth\LoginController@login');

Route::get('registracija/brokeris', 'Auth\RegisterBrokerController@showRegistrationForm')->name('register.broker');
Route::post('registracija/brokeris', 'Auth\RegisterBrokerController@register');

Route::get('registracija/vartotojas', 'Auth\RegisterRegularController@showRegistrationForm')->name('register.regular');
Route::post('registracija/vartotojas', 'Auth\RegisterRegularController@register');

Route::get('patvirtinti-vartotoja/{token}', 'Auth\ConfirmUserController@index')->name('register.confirmation');

// Authentication routes
Route::group(['prefix' => 'auth'], function() {
    // Broker registration
    Route::get('slaptazodzio-priminimas', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.refresh');
    Route::post('slaptazodzio-priminimas', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

    Route::get('slaptazodzio-nustatymas', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('slaptazodzio-nustatymas', 'Auth\ResetPasswordController@reset')->name('password.update');

    Route::group(['middleware' => ['auth']], function ()
    {
       Route::get('logout', 'Auth\LoginController@logout')->name('logout');
    });
});


// PayPal START
//Route::post('/payment/{order}/paypal', [
//    'name'  => 'PayPal Express Checkout',
//    'as'    => 'payment.paypal',
//    'uses'  => 'PayPalController@checkout'
//]);
//
//Route::get('/paypal/checkout/{order}/completed', [
//    'name'  => 'PayPal Express Checkout Completed',
//    'as'    => 'checkout.completed.paypal',
//    'uses'  => 'PayPalController@completed'
//]);
//
//Route::get('/paypal/checkout/{order}/cancelled', [
//    'name'  => 'PayPal Express Checkout Cancelled',
//    'as'    => 'checkout.cancelled.paypal',
//    'uses'  => 'PayPalController@cancelled'
//]);
// PayPal END

// Paysera START
Route::post('/payment/{order}/paysera', [
    'name'  => 'Paysera Checkout',
    'as'    => 'payment.paysera',
    'uses'  => 'PayseraController@checkout'
]);

Route::get('/paysera/checkout/{order}/completed', [
    'name'  => 'Paysera Checkout Completed',
    'as'    => 'checkout.completed.paysera',
    'uses'  => 'PayseraController@completed'
]);

Route::get('/paysera/checkout/{order}/completed/view', 'PayseraController@done')->name('checkout.completed.paysera.view');

Route::get('/paysera/checkout/{order}/cancelled', [
    'name'  => 'Paysera Checkout Cancelled',
    'as'    => 'checkout.cancelled.paysera',
    'uses'  => 'PayseraController@cancelled'
]);

Route::get('/paysera/checkout/{order}/successful', [
    'name'  => 'Paysera Checkout successful',
    'as'    => 'checkout.notify.paysera',
    'uses'  => 'PayseraController@notify'
]);

// Paysera END


// Configs
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    // return what you want
});

Route::get('/{slug}', 'PageController@index');
Route::get('/admin/chain', 'ChainController@index');
Route::get('/admin/chain/getdata', 'ChainController@getChainData');
Route::post('/admin/chain/save', 'ChainController@save');
Route::post('/admin/chain/create/propertytype', 'ChainController@createPropertyType');
Route::post('/admin/chain/create/property', 'ChainController@createProperty');
Route::post('/admin/chain/create/service', 'ChainController@createService');
Route::post('/admin/chain/create/location', 'ChainController@createLocation');