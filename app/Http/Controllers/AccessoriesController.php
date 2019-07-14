<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Accessory;
use Validator;

class AccessoriesController extends Controller
{
  /**
  * create Accessory controller
  * add Accessory's feilds to Database
  * request: POST
  * @param Request
  * @return Response
  */
  public function create(Request $request){
    $validator = Validator::make($request->all(), [
      'title' => 'required',
      'price' => 'required',
      'category' => 'nullable',
      'number_of_sells' => 'nullable',
    ]);

    if ($validator->fails()) {
      $response['status'] = 401;
      $response['data'] = ['errors' => $validator->errors()];
      return response()->json($response, 401);
    }

    $newAccessory = new Accessory;
    $newAccessory->title = $request->title;
    $newAccessory->price = $request->price;
    $newAccessory->category = $request->category;
    $newAccessory->save();

    $response['status'] = 200;
    return response()->json($response, 200);
  }

  /**
  * show Accessory Controller
  * show Accessory's properties by idea
  * request: GET
  * @param int
  * @return Response
  */
  public function show($id){
    $accessory = Accessory::find($id);
    if (!$accessory){
      $response['status'] = 404;
      $response['data'] = ['error' => 'Accessory Not Found'];
      return response()->json($response, 404);
    }

    $data = [
      'title' => $accessory->title,
      'price' => $accessory->price,
    ];
    $response['status'] = 200;
    $response['data'] = $data;
    return response()->json($response, 200);
  }

  /**
  * show all accessories, 10 by each page
  * show id, title and price of each accessory
  * request: GET
  * @return Response
  */
  public function showAll() {
    $accessories = new Accessory;
    $accessories = $accessories->paginate(10);
    $lastPage = $accessories->lastPage();
    $accessoriesResult = array();
    $counter = 0;

    foreach ($accessories as $accessory) {
      $accessoriesArray = [
        'id' => $accessory->id,
        'title' => $accessory->title,
        'price' => $accessory->price,
      ];
      $accessoriesResult += [$counter => $accessoriesArray];
      $counter++;
    }

    $response['status'] = 200;
    $response['data'] = $accessoriesResult + ['last_page' => $lastPage];

    return response()->json($response, 200);
  }
}
