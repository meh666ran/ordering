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
        'weights' => 'required|array',
        'number_of_sells' => 'nullable',
        'cake_image' => 'image|nullable|max:1999',
      ]);

      if ($validator->fails()) {
        $response['status'] = 401;
        $response['data'] = ['errors' => $validator->errors()];
        return response()->json($response, 401);
      }

      if (!$request->hasFile('cake_image')){
        $cakeImage = 'noimage.jpg';
      }
      else {
        $fileNameWithExt = $request->file('cake_image')->getClientOriginalName();
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $extension = $request->file('cake_image')->getClientOriginalExtension();
        $cakeImage = $fileName . '_' . time() . '.' . $extension;
        $path = $request->file('cake_image')->storeAs('public/cake_images', $cakeImage);
      }

      $newCake = new Cake;
      $newCake->name = $request->title;
      $newCake->price = $request->price;
      $newCake->main_category = $request->main_category;
      $newCake->sub_category = $request->sub_category;
      $newCake->weights = $request->weights;
      $newCake->admin_id = $admin->id;
      $newCake->cake_image = $cakeImage;
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

      $weights = $cake->weights;
      for ($index = count($cake->weights); $index < 9; $index++) {
        $weights[$index] = Null;
      }

      $values = [
        'name' => $cake->name,
        'price' => $cake->price,
        'weights' => $weights,
        'image' => asset('/storage/cake_images/' . $cake->cake_image),
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
          'image' => asset('/storage/cake_images/' . $cake->cake_image),
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
      $admin = Auth::guard('admin-api')->user();
      if(!$admin){
        $response['status'] = 401;
        $response['data'] = ['error' => 'token is not valid'];
        return response()->json($response, 401);
      }
      if (!$cake){
        $response['status'] = 404;
        $response['data'] = ['error' => 'cake not found!'];
        return response()->json($response, 404);
      }

      $request = $request->toArray();
      if ( isset($request['title']) ) { $request['name'] = $request['title']; }
      unset($request['title']);
      $cake->update($request);
      $response['status'] = 200;
      return response()->json($response, 200);

    }

    /**
      * update cake's image
      * Request: POST
      * @param Request
      * @param int
      * @return Response
    */
    public function updateImage(Request $request, $id) {
      $cake = Cake::find($id);
      $admin = Auth::guard('admin-api')->user();

      if (!$admin) {
        $response['status'] = 401;
        $response['data'] = ['error' => 'token is not valid'];
        return response()->json($response, 401);
      }

      if (!$cake){
        $response['status'] = 404;
        $response['data'] = ['error' => 'cake not found!'];
        return response()->json($response, 404);
      }

      if ($request->hasFile('cake_image')){
        $fileNameWithExt = $request->file('cake_image')->getClientOriginalName();
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $extension = $request->file('cake_image')->getClientOriginalExtension();
        $cakeImage = $fileName . '_' . time() . '.' . $extension;
        $path = $request->file('cake_image')->storeAs('public/cake_images', $cakeImage);
      }

      $cake->cake_image = $cakeImage;
      $cake->save();
      $response['status'] = 200;
      return response()->json($response, 200);

    }

    /**
      * delete cake by giving id
      * Request = DELETE
      * @param int
      * @return Response
    */
    public function destroy($id) {
      if ( !$admin = Auth::guard('admin-api')->user() ){
        $response['status'] = 401;
        $response['data'] = ['error' => 'admin token is not valid'];
        return response()->json($response, 404);
      }
      if ( $cake = Cake::find($id) ) {
        $cake->delete();
        $response['status'] = 200;
        return response()->json($response, 200);
      }
      else {
        $response['status'] = 404;
        return response()->json($response, 404);
      }
    }

}
