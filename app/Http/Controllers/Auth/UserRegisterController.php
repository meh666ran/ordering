<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Hash;
use App\User;

class UserRegisterController extends Controller
{
  public $successStatus = 200;

  public function __construct() {
    $this->middleware('guest');
  }

  public function register(Request $request) {
    $validator = Validator::make($request->all(), [
      'name' => 'required|max:255',
      'phone_number' => 'required',
      'password' => 'required|min:8',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 401);
    }

    $input = $request->all();
    $input['password'] = Hash::make($input['password']);
    $user = User::create($input);
    $success['token'] = $user->createToken('ordering')->accessToken;
    $success['name'] = $user->name;
    return response()->json(['success' => $success]);

  }

}
