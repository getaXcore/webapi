<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'v1','middleware' => ['basicAuth']],function ($router){
    Route::post('regrenov','Renov\RenovController@regRenov');
	Route::post('regrestruk','Restruk\RestrukController@regRestruk');
	Route::get('budget','Agri\AgriController@getBudget');
	Route::get('vehicle','Agri\AgriController@getVehicle');
	Route::post('imgvehicle','Agri\AgriController@getVehicleImage');
    Route::post('validate','Ektp\Ektp@checkOnTable');
    Route::post('fetching','Ektp\Ektp@fetching');
	
	//ASM X JTO
	Route::post('checkon','Asm\Asm@check');
});

Route::get('v1/ektp','Ektp\Ektp@checkTemplateShort');
Route::get('asm','Asm\Asm@index');
//Route::get('gmd','Asm\Asm@gmd5');

/*Route::get('excel','ExcelClass@index');
Route::get('txt','ExcelClass@readTxt');
Route::get('ektp','Ektp\Ektp@index');
*/

Route::get('dttot','Dttot\DttotController@index');
Route::post('dttot','Dttot\DttotController@index');
Route::get('cetak','Dttot\DttotController@toprintpage');

Route::get('ocitest','Ektp\Ektp@tesOci');
Route::get('ins','Insur\InsurController@index');
Route::get('excelTest1/{id}','Exceltest\ExcelController@loadTo');
Route::get('artg','Job\JobController@jobArTunggak');
Route::get('paydeb/{id}','Job\JobController@loadTo');
Route::get('jobexe','Job\JobController@cronjob');
Route::get('payfinjob','Job\JobController@finjob');
Route::get('a','FirstController@index');
Route::post('hit','FirstController@hit');
Route::post('karyawan/add','FirstController@add');
Route::get('karyawan/all','FirstController@getAll');
Route::post('karyawan/update','FirstController@update');
Route::get('karyawan/delete/{id}','FirstController@del');
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

/*Route::group([
    /*'middleware' => ['api', 'cors'],
    'namespace' => $this->namespace,*/
    /*'prefix' => 'inq'
], function ($router) {
    //Balance
    Route::post('/balance','Inq\InquiryController@getBalance');

    //Mutasi
    Route::post('/statement','Inq\InquiryController@getStatement');

    //Transfer
    Route::post('/transfer','Tran\TransferController@doTransfer');
});*/

/*Route::group(['prefix' => 'auth'], function () {
    //auth
    Route::get('/getToken','Auth\AuthController@auth');
    //check expire
    Route::get('/cToken','Auth\AuthController@checkExpireOf');
    //signature
    Route::get('/signature','Auth\AuthController@signature');
});*/
