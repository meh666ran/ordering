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
    $this->middleware('guest:user');
  }

  public function register(Request $request) {
    $validator = Validator::make($request->all(), [
      'name' => 'required|max:255',
      'phone_number' => 'required|unique:users',
      'password' => 'required|min:8',
      'address' => 'nullable',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 401);
    }

    $input = $request->all();
    $input['password'] = Hash::make($input['password']);

    $user = User::create($input);

    $success['status'] = '200';
    $success['data'] = [
      'token' => $user->createToken('ordering-user-token')->accessToken,
      'name' => $user->name,
    ];

    return response()->json($success, 200);

  }

}
