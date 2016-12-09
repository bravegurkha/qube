<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use \App\Messages;

class MessageController extends Controller
{
  public function send(Request $request)
  {
      $validator = \Validator::make(
      array(
        'from' => $request->from,
        'to' => $request->to,
        'message' => $request->message,
      ),
      array(
        'from' => 'required|exists:users,id',
        'to' => 'required|exists:users,id',
        'message' => 'required',
      ));

      if($validator->fails()){
          return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
      }

      $message = new Messages();
      $message->from = $request->from;
      $message->to = $request->to;
      $message->message = $request->message;
      $message->save();

      return response()->json(['success' => true, 'data' => "message_sent", 'status' => 200]);
  }

  public function get(Request $request)
  {
    $validator = \Validator::make(
    array(
      'from' => $request->from,
      'to' => $request->to,
    ),
    array(
      'from' => 'required|exists:users,id',
      'to' => 'required|exists:users,id',
    ));

    if($validator->fails()){
        return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
    }

    $messages = Messages::where('from',$request->from)->where('to',$request->to)->get();
    return response()->json(['success' => true, 'data' => $messages, 'status' => 200]);
  }
}
