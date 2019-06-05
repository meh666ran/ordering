<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class AdminLoginController extends Controller
{

  public function __construct() {
    $this->middleware('guest');
  }

    // not important
    public function showLoginForm () {
      return view("auth.adminlogin");
    }


    public function login (Request $request) {
      // validate the form data
      $this->validate($request, [
        'email' => 'required|email',
        'password' => 'required|min:8',
        ] );

        // user credentials
        $credentials = [
          'email' => $request->email,
          'password' => $request->password,
        ];

        // attempt to log user in
        if (Auth::guard('admin')->attempt($credentials)) {
          return 'true';
          // return redirect()->intended(route('admin.dashboard'));
        }

        // if login was unsuccessful
        return 'false';
    }


}
