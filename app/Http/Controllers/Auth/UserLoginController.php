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
      $this->middleware('guest:user');
    }

    public function login(Request $request) {
      // validate form data
      $validator = Validator::make($request->all(), [
        'phone_number' => 'required',
        'password' => 'required|min:8',
      ]);

      if ( $validator->fails() ) {
          $response['status'] = 401;
          $response['data'] = ['errors' => $validator->errors()];
          return response()->json($response, 401);
      }

      // user credentials
      $credentials = [
        'phone_number' => $request->phone_number,
        'password' => $request->password,
      ];

      if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $successToken = $user->createToken('ordering-user-token')->accessToken;
        $response['status'] = 200;
        $response['data'] = ['token' => $successToken];
        return response()->json($response, $this->successStatus);
      }
      else {
          $response['status'] = 401;
          $response['data'] = ['error' => 'Phone Number or Password isn\'t Valid'];
          return response()->json($response, 401);
      }

    }

    public function details() {
      $user = Auth::guard('api')->user();
      if (!$user){
        $response['status'] = 401;
        $response['data'] = ['error' => 'token is not valid'];
        return response()->json($response, 401);
      }
      else {
        $response['status'] = 200;
        $response['data'] = $user;
        return response()->json($response, $this->successStatus);
      }
    }

}
