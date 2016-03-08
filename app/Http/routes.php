<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
 
 /*authenticate user :| */
 Route::get('login', 'UserController@getLogin');
 Route::post('user/auth', 'UserController@authenticate');
 Route::get('logout', 'UserController@getLogout');

/*protect route from uninvited guess D: grrr~*/
Route::group(['middleware' => 'auth'], function () {

	Route::get('issue/export', 'ExportController@index');
	Route::post('issue/export', 'ExportController@exportexcel');

	Route::get('/', function () {
   	 return redirect('summary');
	});

	Route::get('/test', function(){
		return view('issue.manager');
	});
   
	Route::get('summary', 'SummaryController@index');
	
	/*Issue data routes ~*/
	Route::post('issue/search/', 'IssueController@search');
	
	Route::get('issue/priority/list/', 'IssueController@prioritylist');
	Route::get('issue/status/open', 'IssueController@open');
	Route::get('issue/status/closed', 'IssueController@closed');

	Route::get('issue/status/open/section/{sect_id}', 'IssueController@opensection');
	Route::get('issue/status/closed/section/{sect_id}', 'IssueController@closedsection');
	Route::get('issue/status/all/section/{sect_id}', 'IssueController@totalsection');

	Route::get('issue/section/{id}', 'IssueController@section');
	Route::resource('issue', 'IssueController');
	Route::post('issue/priority/{level}/{id}', 'IssueController@changepriority');

	Route::get('user/editprofile/', 'UserController@editprofile');
	Route::patch('user/editprofile/changepass/{id}', 'UserController@userchangepassword');

	Route::group(['middleware' => 'role:admin'], function () {
		/*user routes ~ */
		Route::post('user/search/', 'UserController@search');

		Route::patch('user/changepass/{id}', 'UserController@changepass');
		
		Route::get('user/settings/{id}', 'SummaryController@settings');

		Route::resource('user', 'UserController');
	});


	
});


