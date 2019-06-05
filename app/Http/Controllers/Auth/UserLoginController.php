<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Validator;

class UserLoginController extends Controller
{
    public $successStatus = 200;

    public function __construct() {
      $this->middleware('guest');
    }

    public function login(Request $request) {
      // validate form data
      $validator = Validator::make($request->all(), [
        'phone_number' => 'required',
        'password' => 'required|min:8',
      ]);

      if ( $validator->fails() ) {
          return response()->json(['error' => $validator->errors()], 401);
      }

      // user credentials
      $credentials = [
        'phone_number' => $request->phone_number,
        'password' => $request->password,
      ];

      if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $success = $user->createToken('ordering')->accessToken;
        return response()->json(['success' => 'true', 'token' => $success], $this->successStatus);
      }
      else {
          return response()->json(['error' => 'Unauthorised'], 401);
      }

    }

}
