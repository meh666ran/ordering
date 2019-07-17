<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use Validator;
use App\Cake;

class OrdersController extends Controller
{

    /**
     * create an order
     * save order's information into database
     * @param Request
     * @return Response
    */
    public function create(Request $request) {
      $user = Auth::guard('api')->user();
      if(!$user){
        $response['status'] = 401;
        $response['data'] = ['error' => 'User Token is not valid!'];
        return response()->json($response, 401);
      }
      $userID = $user->id;

      $validator = Validator::make($request->all(), [
        'title' => 'nullable',
        'cake_id' => 'required|Integer',
        'weight' => 'required',
        'final_price' => 'required',
        'send_date_time' => 'required',
        'isReady' => 'nullable|boolean',
        'isDeliverd' => 'nullable|boolean',
        'text_on_cake' => 'nullable',
        'description' => 'nullable',
      ]);

      if($validator->fails()) {
        $response['status'] = 401;
        $response['data'] = ['errors' => $validator->errors()];
        return response()->json($response, 401);
      }

      $cake = Cake::find($request->cake_id);
      if (!$cake) {
        return response()->json([
          'status' => '404',
          'data' => ['error' => 'cake not found. id is invalid'],
        ], 404);
      }
      $request['title'] = $cake->name;

      $order = new Order;
      $order->title = $request->title;
      $order->cake_id = $request->cake_id;
      $order->weight = $request->weight;
      $order->final_price = $request->final_price;
      $order->send_date_time = $request->send_date_time;
      $order->text_on_cake = $request->text_on_cake;
      $order->description = $request->description;
      $order->user_id = $userID;
      $order->save();

      $this->incNumberOfSells($request->cake_id);

      return response()->json(['status' => '200'], 200);
    }

    /**
     * increase number of selles field of cake ordered
     * @param int
    */
    public function incNumberOfSells($id) {
      $cake = Cake::find($id);
      $cake->number_of_sells += 1;
      $cake->save();
    }
}
