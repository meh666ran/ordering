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

      $admin = Auth::guard('admin-api')->user();
      if(!$admin){
        $response['status'] = 401;
        $response['data'] = ['error' => 'admin token is not valid'];
        return response()->json($response, 401);
      }

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
      $newCake->admin_id = $admin->id;
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
    * create response arrays to show in products page
    * @param object
    * @return array
    */
    public function createProductsPageArray($cakes) {
      $lastPage = $cakes->lastPage();
      $cakesResult = array();
      $counter = 0;

      foreach($cakes as $cake) {
        $cakeArr = [
          'id' => $cake->id,
          'name' => $cake->name,
          'price' => $cake->price,
        ];

        $cakesResult += [$counter => $cakeArr];
        $counter++;
      }

      $response['status'] = 200;
      $response['data'] = $cakesResult + ['last_page' => $lastPage];

      return $response;
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

      // TODO: Change to switch case
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
      $response = $this->createProductsPageArray($cakes);
      return response()->json($response, 200);
    }

    /**
    * show cakes by category
    * show cakes name and price per killo and id
    * @param string
    * @return response
    */
    public function showByCategory($category){
      $cakes = Cake::where('main_category', $category)
        ->orWhere('sub_category', $category)
        ->paginate(10);

        $response = $this->createProductsPageArray($cakes);
        return response()->json($response, 200);

    }

    /**
      * edit cake's fields by given id
      * Request: PUT
      * @param Request
      * @param int
      * @return Response
    */
    public function update(Request $request, $id){
      $cake = Cake::find($id);
      if (!$cake){
        $response['status'] = 404;
        $response['data'] = ['error' => 'cake not found!'];
        return response()->json($response, 404);
      }

      // TODO: change this to switch case
      if (!$request->input('title')){

      }
      else {
        $cake->name = $request->input('title');
      }

      if (!$request->input('price')){

      }
      else {
        $cake->price = $request->input('price');
      }

      if (!$request->input('main_category')){

      }
      else {
        $cake->main_category = $request->input('main_category');
      }

      if (!$request->input('sub_category')){

      }
      else {
        $cake->sub_category = $request->input('sub_category');
      }

      if (!$request->input('weights')){

      }
      else {
        $cake->weights = $request->input('weights');
      }

      $cake->save();
      $response['status'] = 200;
      return response()->json($response, 200);

    }
}
