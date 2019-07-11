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
        $response['status'] = 401;
        $response['data'] = ['errors' => $validator->errors()];
        return response()->json($response, 401);
      }

      $newCake = new Cake;
      $newCake->name = $request->title;
      $newCake->price = $request->price;
      $newCake->main_category = $request->main_category;
      $newCake->sub_category = $request->sub_category;
      $newCake->weights = $request->weights;
      $newCake->admin_id = Auth::guard('admin-api')->user()->id;
      $newCake->save();

      $response['status'] = 200;
      return response()->json($response, 200);

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
        $response['status'] = 404;
        $response['data'] = ['error' =>'Cake Not Found'];
        return response()->json($response, 404);
      }

      $values = [
        'name' => $cake->name,
        'price' => $cake->price,
        'weights' => $cake->weights,
      ];
      $response['status'] = 200;
      $response['data'] = $values;
      return response()->json($response, 200);
    }

    /**
    * show ordered products 10 by each page
    * shows cakes' name and price per killo
    * @param string
    * @return Response
    */
    public function productsPage($order) {

      $orderBy = null;
      $arrange = null;

      if ($order == 'most-sold') {
        $orderBy = 'number_of_sells';
        $arrange = 'desc';
      }
      else if ($order == 'cheapest') {
        $orderBy = 'price';
        $arrange = 'asc';
      }
      else if ($order == 'newest') {
        $orderBy = 'created_at';
        $arrange = 'desc';
      }
      else {
        $response['status'] = 404;
        $response['data'] = ['error' => 'Order is not Valid'];
        return response()->json($response, 404);
      }

      $cakes = Cake::orderBy($orderBy, $arrange)->paginate(10);
      $cakesResult = array();
      $counter = 0;

      foreach ($cakes as $cake) {
        $cakeArr = [
          'id' => $cake->id,
          'name' => $cake->name,
          'price' => $cake->price,
        ];

        $result['status'] = 200;
        $cakesResult += [$counter => $cakeArr];
        $result['data'] = $cakesResult;
        $counter++;
      }
      return response()->json($result, 200);
    }
}
