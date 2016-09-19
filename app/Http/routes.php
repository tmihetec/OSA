<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/


//Route::get('setup', 'SetupController@setupRoles');


/* SOAP */
// TEMP Client route:
Route::get('newOrder', 'SoapClientController@newOrder'); // <-- samo za test, ovo dolazi s njihovog servera

//Client route
//Route::get('orderUpdate/{caseId}:{repairStatus}', 'SoapClientController@callOrderUpdate'); // <-- ne treba ruta, poziva se iz kontrolera nakon promjene statusa!

//Server route:
Route::post('repairOrder', 'SoapServerController@soapRequest');
// ==========================================================================


/* PROVJERA IMEI STATUSA */
Route::get('', array('as' => 'slucaj.check', 'uses' => 'RepairorderController@startscreen'));
Route::get('imstat', 'RepairorderController@startscreen');
Route::post('imstat', 'RepairorderController@startscreen');




Route::group(array('middleware' => 'auth'), function () {


    /*

        Route::controller('datatables', 'DatatablesController', [
            'postArhiva'  => 'dataarhiva',
            'postOtvoreno'=> 'datatables.otvoreno'
        ]);
     */


    Route::post('dohvatiArhivaNaloge', 'DatatablesController@dohvatiArhivaNaloge'); // <!-- otprema nalog, ajax call
    Route::post('dohvatiNalogeZaOne', 'ReportController@dohvatiNalogeZaOne'); // <!-- reportONE, ajax call


    // TEMP... poslati u T2 zadnje statuse
    Route::get('slucaj/catchupT2', 'RepairorderController@catchupT2'); // <!-- otprema nalog, ajax call


    Route::get('slucaj/arhiva', 'RepairorderController@arhiva'); // <!-- otprema nalog, ajax call

    Route::get('slucaj/createFromSoap/{t2s}', 'RepairorderController@createFromSoap'); // <-- ne treba ruta, poziva se iz kontrolera nakon promjene statusa!
    Route::post('slucaj/rejectSoapCase/{t2s}', 'RepairorderController@rejectSoapCase'); // <-- ne treba ruta, poziva se iz kontrolera nakon promjene statusa!
    Route::post('slucaj/otpremi/{id}', 'RepairorderController@otpremiNalog'); // <!-- otprema nalog, ajax call
    Route::post('slucaj/relociraj/{id}', 'RepairorderController@relocirajNalog'); // <!-- otprema nalog, ajax call

    Route::get('dashboard', 'DashboardController@show');

    Route::get('t2nalozi', 'RepairorderController@showTele2Naloge');
    Route::get('SPPnalozi', 'RepairorderController@showSPPNaloge');


    Route::resource('slucaj', 'RepairorderController');
    Route::resource('komitenti', 'PrincipalController');
    Route::resource('primke', 'ReceiptController');
    Route::resource('prodajnamjesta', 'PosController');
    Route::resource('rezervnidijelovi', 'SparepartController');
    Route::resource('usluge', 'ServicesController');
    Route::resource('brandovi', 'BrandController');
    Route::resource('tipovi', 'TipController');
    Route::resource('modeli', 'ModelController');
    Route::resource('zaposlenici', 'EmployeeController');
    Route::resource('gradovi', 'LocplacesController');

    Route::get('reportOne', 'ReportController@showOne');
    Route::post('reportOne', 'ReportController@doOne');

    Route::get('reportOtprema', 'ReportController@showOtprema');
    Route::post('reportOtprema', 'ReportController@doOtprema');

    Route::get('reportRealizacija', 'ReportController@showRealizacija');
    Route::post('reportRealizacija', 'ReportController@doRealizacija');

    Route::get('reportRealizacijaDetaljno/{user}/{datumOd}/{datumDo}', 'ReportController@makeRealizacijaDetaljno');
    Route::get('reportRealizacijaDetaljno', 'ReportController@showRealizacijaDetaljno');
    Route::post('reportRealizacijaDetaljno', 'ReportController@doRealizacijaDetaljno');
    //Route::get('reportOtpremljeno', 'ReportController@showOtpremljeno');
    //Route::post('reportOtpremljeno', 'ReportController@doOtpremljeno');

    Route::post('dajStatuseNaloga', 'ReportController@dajStatuseNaloga');

    Route::get('printView/rn/{id}', 'RepairorderController@printView_radniNalog');
    Route::get('pdfView/rn/{id}', 'RepairorderController@pdfView_radniNalog');
    Route::get('printPrijemniView/rn/{id}', 'RepairorderController@printView_prijemniList');
    Route::get('pdfPrijemniView/rn/{id}', 'RepairorderController@pdfView_prijemniList');


    Route::post('detailsModalBtn', 'ModalController@detailsModalBtn');
    Route::get('drawHistoryModal/{id}', 'ModalController@iscrtajHistoryModal');
    Route::get('drawExservisiModal/{value}', 'ModalController@iscrtajExservisiModal');






    /* REPORT MW */
    Route::get('getmwrpt', 'MwrptController@startscreen');
    Route::post('getmwrpt', 'MwrptController@getreport');




});


// ===============================================
// LOGIN SECTION =================================
// ===============================================

// route to show the login form
Route::get('login', array('uses' => 'LoginController@showLogin'));
Route::get('auth/login', array('uses' => 'LoginController@showLogin'));
// route to process the form
Route::post('auth/login', array('uses' => 'LoginController@doLogin'));
// route to logout
// Ideally, this route would be a POST route for security purposes. This will ensure that your logout won’t be accidentally triggere http://stackoverflow.com/questions/3521290/logout-get-or-post
Route::get('logout', array('uses' => 'LoginController@doLogout', 'as' => 'logout'));


// Registration routes...
//Route::get('auth/register', 'Auth\AuthController@getRegister');
//Route::post('auth/register', 'Auth\AuthController@postRegister');

//Route::auth();

/*
staro
/// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');
*/

// Password Reset Routes...
Route::post('password/reset', 'Auth\PasswordController@reset');
Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');

Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');



