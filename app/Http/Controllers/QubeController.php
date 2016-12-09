<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Qube;
use Intervention\Image\Facades\Image as Image;
use Carbon\Carbon;
use \App\QubeLikes;

class QubeController extends Controller
{

    public function makeQubes(Request $request)
    {
        $validator = \Validator::make(
        array(
            'user_id' => $request->user_id,
            'image' => $request->image,
            'title' => $request->title,
        ),
        array(
            'user_id' => 'required|exists:users,id',
            'image' => 'required|image',
            'title' => 'required',
        ));

        if($validator->fails())
        {
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }

        $file_name = rand(10000,1000000000).'_'.time();
        $path =public_path('images/qubes/'.$file_name.'.jpg');
        $url = url('images/qubes/'.$file_name.'.jpg');
        if($request->hasFile('image')){
            $file = $request->file('image');
            $img = Image::make($file->getRealPath())->resize(400,400);
            $img->save($url);
        }

        $qube = new Qube();
        $qube->user_id = $request->user_id;
        $qube->image = 'images/qubes/'.$file_name.'.jpg';
        $qube->title = $request->title;
        $qube->save();

        return response()->json(['success' => true, 'data' => "qube saved", 'status' => 200]);
    }

    public function getQubes(Request $request)
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

        $qubes = Qube::where('user_id',$request->user_id)->get();
        return response()->json(['success' => true, 'data' => $qubes, 'status' => 200]);
    }

    public function like(Request $request)
    {
        $validator = \Validator::make(
        array(
        'user_id' => $request->user_id,
        'qube_id' => $request->qube_id,
        ),
        array(
        'user_id' => 'required|exists:users,id',
        'qube_id' => 'required|exists:qubes,id',
        )
        );

        if($validator->fails()){
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }

        $post_likes = new PostLikes();
        $post_likes->user_id = $request->user_id;
        $post_likes->qube_id = $request->qube_id;
        $post_likes->save();

        return response()->json(['success' => true, 'data' => "liked", 'status' => 200]);
    }

    public function getLikes(Request $request)
    {
        $validator = \Validator::make(
        array(
        'qube_id' => $request->qube_id,
        ),
        array(
        'qube_id' => 'required|exists:qubes,id',
        )
        );

        if($validator->fails()){
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }
        $likes = QubeLikes::where('qube_id',$request->qube_id)->count();
        return response()->json(['success' => true, 'data' => $likes, 'status' => 200]);
    }
}
