<?php

namespace App\Http\Controllers;

use App\Posts;
use Illuminate\Http\Request;
use App\PostLikes;

class PostController extends Controller
{
    public function post(Request $request){
        $validator = \Validator::make(
        array(
            'user_id' => $request->user_id,
            'title' => $request->title,
            'description' => $request->description,
        ),
        array(
            'user_id' => 'required|exists:users,id',
            'title' => 'required|min:2',
            'description' => 'required|min:16',
        )
    );

    if ($validator->fails()) {
        return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
    }

    $post = new Posts();
    $post->user_id = $request->user_id;
    $post->title = $request->title;
    $post->description = $request->description;
    $post->save();

    return response()->json(['success' => true, 'data' => 'posted', 'status' => 201]);
}

public function getPosts(Request $request){
    $validator = \Validator::make(
    array(
        'user_id' => $request->user_id,
    ),
    array(
        'user_id' => 'required|exists:users,id',
    ));

    if ($validator->fails()){
        return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
    }

    $posts = Posts::where('user_id', $request->user_id)->get();

    return response()->json(['success' => true, 'data' => $posts, 'status' => 200]);
}

public function getPost(Request $request)
{
    $validator = \Validator::make(
    array(
    'id' => $request->post_id,
    ),
    array(
    'id' => 'required|exists:posts,id',
    )
    );

    if ($validator->fails()) {
        return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
    }

    $post = Posts::where('id', $request->id)->get()->first();

    return response()->json(['success' => true, 'data' => $post, 'status' => 200]);
}

public function like(Request $request)
{
    $validator = \Validator::make(
    array(
    'user_id' => $request->user_id,
    'post_id' => $request->post_id,
    ),
    array(
    'user_id' => 'required|exists:users,id',
    'post_id' => 'required|exists:posts,id',
    ));

    if ($validator->fails()) {
        return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
    }

    $post_likes = new PostLikes();
    $post_likes->user_id = $request->user_id;
    $post_likes->post_id = $request->post_id;
    $post_likes->save();

    return response()->json(['success' => true, 'data' => 'liked', 'status' => 201]);
}

public function getLikes(Request $request)
{
    $validator = \Validator::make(
    array(
    'post_id' => $request->qube_id,
    ),
    array(
    'post_id' => 'required|exists:qubes,id',
    ));

    if ($validator->fails()) {
        return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
    }
    $likes = PostLikes::where('post_id', $request->post_id)->count();

    return response()->json(['success' => true, 'data' => $likes, 'status' => 200]);
}
}
