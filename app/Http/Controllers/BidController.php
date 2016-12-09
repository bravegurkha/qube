<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use \App\Bids;

class BidController extends Controller
{
  public function bid(Request $request)
  {
    $validator = \Validator::make(
    array(
      'user_id' => $request->user_id,
      'project_id' => $request->project_id,
      'description' => $request->description,
      'price' => $request->price,
    ),
    array(
      'user_id' => 'required|exists:users,id',
      'project_id' => 'required|exists:projects,id',
      'description' => 'required',
      'price' => 'required|numeric',
    ));

    if($validator->fails())
    {
        return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
    }

    $bid = new Bids();
    $bid->user_id = $request->user_id;
    $bid->project_id = $request->project_id;
    $bid->description = $request->description;
    $bid->price = $request->price;
    $bid->save();

    return response()->json(['success' => true, 'data' => "bid registered", 'status' => 200]);
  }

  public function getBidsByProject(Request $request)
  {
      $validator = \Validator::make(
      array(
        'project_id' => $request->project_id,
      ),
      array(
        'project_id' => 'required|exists:projects,id',
     ));

      if($validator->fails())
      {
          return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
      }

    $bids = Bids::where('project_id',$request->project_id)->get();
    return response()->json(['success' => true, 'data' => $bids, 'status' => 200]);
  }

  public function getBidsByUser(Request $request)
  {
      $validator = \Validator::make(
      array(
        'user_id' => $request->user_id,
      ),
      array(
        'user_id' => 'required|exists:users,id',
     ));

      if($validator->fails())
      {
          return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
      }

    $bids = Bids::where('user_id',$request->user_id)->get();
    return response()->json(['success' => true, 'data' => $bids, 'status' => 200]);

  }

  public function getBid(Request $request)
  {
      $validator = \Validator::make(
      array(
        'id' => $request->id,
      ),
      array(
        'id' => 'required|exists:bids,id',
     ));

      if($validator->fails())
      {
          return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
      }

    $bid = Bids::where('id',$request->id)->get();
    return response()->json(['success' => true, 'data' => $bid, 'status' => 200]);

  }
}
