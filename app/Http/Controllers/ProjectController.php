<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use \App\Projects;
use Intervention\Image\Facades\Image as Image;
use Carbon\Carbon;

class ProjectController extends Controller
{
  public function postProject(Request $request)
  {
    $validator = \Validator::make(
    array(
      'user_id' => $request->user_id,
      'title' => $request->title,
      'cat_id' => $request->cat_id,
      'type_id' => $request->type_id,
      'description' => $request->description,
      'image' => $request->image,
      'location' => $request->location,
    ),
    array(
      'user_id' => 'required|exists:users,id',
      'title' => 'required|min:2',
      'cat_id' => 'required|exists:categories,id',
      'type_id' => 'required|exists:types,id',
      'description' => 'required|min:16',
      'location' => 'required|min:2',
      'image' => 'required|image',
    )
  );

  if($validator->fails()){
      return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
  }
  $file_name = rand(10000,1000000000).'_'.time();
  $path = public_path('images/projects/'.$file_name.'.jpg');
  $url = url('images/projects/'.$file_name.'.jpg');
  if($request->hasFile('image')){
    $file = $request->file('image');
    $img = Image::make($file->getRealPath())->resize(400,400);
    $img->save($path);
  }

  $project= new Projects();
  $project->user_id = $request->id;
  $project->title = $request->title;
  $project->category_id = $request->cat_id;
  $project->type_id = $request->type_id;
  $project->description = $request->description;
  $project->image = $url;
  $project->location = $request->location;
  $project->save();

  return response()->json(['success' => true, 'data' => "project saved", 'status' => 200]);

}

  public function getProjects(Request $request)
  {
      $validator = \Validator::make(
      array(
        'user_id' => $request->user_id,
      ),
      array(
        'user_id' => 'required|exists:users,id',
      )
    );

    if($validator->fails()){
        return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
    }

    $projects =  Projects::where('user_id',$request->user_id)->get();

    return response()->json(['success' => true, 'data' => $projects, 'status' => 200]);

  }

  public function getProject(Request $request)
  {
      $validator = \Validator::make(
      array(
        'id' => $request->id,
      ),
      array(
        'id' => 'required|exists:projects,id',
      )
    );

    if($validator->fails()){
        return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
    }
    $project = Projects::where('id',$request->id)->first();

    return response()->json(['success' => true, 'data' => $project, 'status' => 200]);
  }
}
