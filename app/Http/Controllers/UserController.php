<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Gate;
use App\User;
use App\Section;
use Auth;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::with('section')->paginate(10);
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {   
        $section = Section::lists('name', 'id');
        return view('user.create', compact('section'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = $this->validate($request, [
            'name'     => 'required|max:255',
            'username' => 'required|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
            'role'     => 'required',
            'section_id' => 'required',
        ]);

        User::create([
            'name'       =>  $request['name'],
            'username'   =>  $request['username'],
            'email'      =>  $request['email'],
            'password'   =>  bcrypt($request['password']),
            'role'       =>  $request['role'],
            'section_id' =>  $request['section_id'],
        ]);

        \Session::flash('flash_message', "new user has been created!");
        return redirect('user');
    }

    /**
     * search data by query in every field of issue
     * @return Response
     */

    public function search(Request $request){
         $users = User::where('name', 'like', "%$request->search%")
                ->orWhere('username', 'like', "%$request->search%")
                ->orWhere('role', 'like', "%$request->search%")
                ->orWhere('email', 'like',"%$request->search%")
                ->paginate(10);

         return view('user.index', compact('users'));
    }

    /**
     * Display the specified resource.
     * currently not used, redirect back! lol
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $section = Section::lists('name', 'id');
        $user = User::findOrFail($id);
        return view('user.edit', compact('user', 'section'));
    }

    public function settings($user)
    {
        $user = User::findOrFail($user);
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //return $request->all();
       $validator = $this->validate($request, [
            'name'     => 'required|max:255',
            'username' => 'required|max:255',
            'role'     => 'required',
       ]);

       $user = User::findOrFail($id);
       $user->update([
        'section_id' => $request['section_id'],
        'name'     =>  $request['name'],
        'username' =>  $request['username'],
        'email'    =>  $request['email'],
        'role'     =>  $request['role'],
       ]);

       \Session::flash('flash_message', "User data has been updated!");

       return back();
    }

    public function changepass(Request $request, $id)
    {
        $validator = $this->validate($request, [
           'password' => 'required|confirmed|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => bcrypt($request['password']),
        ]);

        \Session::flash('change_pass', "User password has been changed!");

        return back();
    }

    public function editprofile()
    {
        return view('user.editprofile');
    }
    

    public function userchangepassword(Request $request, $id)
    {

        $validator = $this->validate($request, [
           'password' => 'required|confirmed|min:6',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'password' => bcrypt($request['password']),
        ]);

        \Session::flash('change_pass', "User password has been changed!");

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
         // delete
        $user = User::find($id);
        $user->delete();

        // redirect
        \Session::flash('flash_message', "User has been succesfully deleted");

        return back();
    }

    public function getLogin(){
        return view('user.login');
    }

    public function authenticate(Request $request)
    {

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            // Authentication passed...
            return redirect()->intended('summary');
        } else {
            \Session::flash('error_message', "Wrong username or password");
            return back();
        }
    }

    public function getLogout(){
        Auth::logout();

        \Session::flash('flash_message', "You have succesfully logging out");

        return redirect('login');
    }

}
