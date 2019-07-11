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
        $response['status'] = 401;
        $response['data'] = ['error' => $validator->errors()];
        return response()->json($response, 401);
      }

        // user credentials
        $credentials = [
          'email' => $request->email,
          'password' => $request->password,
        ];

        // attempt to log user in
        if (Auth::guard('admin')->attempt($credentials)) {
          $user = Auth::guard('admin')->user();
          $success['status'] = 200;
          $successToken = $user->createToken('ordering-admin-token')->accessToken;
          $success['data'] = ['token' => $successToken];
          return response()->json($success, $this->successStatus);
        }
        else {
          // if login was unsuccessful
          $response['status'] = 401;
          $response['data'] = ['error' => 'email or password is invalid'];
          return response()->json( $response, 401 );
        }

    }

    public function details() {
      $user = Auth::guard('admin-api')->user();
      $response['status'] = 200;
      $response['data'] = $user;
      return response()->json($response, $this->successStatus);
    }


}
