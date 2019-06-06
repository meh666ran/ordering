<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Validator;

class AdminLoginController extends Controller
{

  public $successStatus = 200;

  public function __construct() {
    $this->middleware('guest:admin');
  }


    public function login (Request $request) {

      // validate the form data
      $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|min:8',
      ]);

      if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 401);
      }

        // user credentials
        $credentials = [
          'email' => $request->email,
          'password' => $request->password,
        ];

        // attempt to log user in
        if (Auth::guard('admin')->attempt($credentials)) {
          $user = Auth::guard('admin')->user();
          $success = $user->createToken('ordering-admin-token')->accessToken;
          return response()->json(['success' => 'true', 'token' => $success], $this->successStatus);
        }
        else {
          // if login was unsuccessful
          return response()->json( ['error' => 'Unauthorised'], 401 );
        }

    }

    public function details() {
      $user = Auth::guard('admin-api')->user();
      return response()->json(['success' => $user], $this->successStatus);
    }


}
