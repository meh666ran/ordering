<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Cake;

class CakesController extends Controller
{

  /**
  * create cake function
  * add cake's feilds to Database
  * request: POST
  * @param Request
  * @return Response
  */
    public function create(Request $request) {

      $validator = Validator::make($request->all(), [
        'title' => 'required',
        'price' => 'required',
        'main_category' => 'required',
        'sub_category' => 'required',
        'weights' => 'required',
        'number_of_sells' => 'nullable',
      ]);

      if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 401);
      }

      $newCake = new Cake;
      $newCake->name = $request->title;
      $newCake->price = $request->price;
      $newCake->main_category = $request->main_category;
      $newCake->sub_category = $request->sub_category;
      $newCake->weights = $request->weights;
      $newCake->admin_id = Auth::guard('admin-api')->user()->id;
      $newCake->save();


      $success['status'] = 'done';
      return response()->json(['success' => $success], 200);

    }

    /**
    * show cake function
    * shows cakes properties by id
    * request: GET
    * @param int
    * @return Response
    */
    public function show($id) {
      $cake = Cake::find($id);

      if (!$cake) {
        return response()->json(['error' => 'cake not found'], 404);
      }

      $values = [
        'name' => $cake->name,
        'price' => $cake->price,
        'weights' => $cake->weights,
      ];
      return response()->json($values, 200);
    }

