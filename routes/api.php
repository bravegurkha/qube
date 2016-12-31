<?php

use Illuminate\Http\Request;
// API Routes

Route::post('auth','Api\AuthController@authenticate');
Route::post('signup','Api\UserController@partialSignup');
Route::post('reset','Api\UserController@reset');
Route::post('reset/confirm','Api\UserController@resetConfirm');

Route::get('users/near','Api\UserController@getNearUsers');
Route::get('user/followers','Api\UserController@followers');
Route::get('user/followed','Api\UserController@followed');

Route::get('projects','ProjectController@getProjects');
Route::get('project','ProjectController@getProject');

Route::get('posts','PostController@getPosts');
Route::get('post','PostController@getPost');
Route::get('post/likes','PostController@getLikes');
Route::get('comments','CommentController@getComments');

Route::get('qubes','QubeController@getQubes');
Route::get('qube/likes','QubeController@getLikes');

Route::group(['middleware' => 'jwt.auth'],function(){
  //Users ROutes
  Route::get('user/me','Api\AuthController@getAuthenticatedUser');
  Route::get('user','Api\UserController@getUser');
  Route::post('user/update/','Api\UserController@completeAccount');
  Route::post('user/follow','Api\UserController@follow');

  //Notification Routes
  Route::get('/user/notification','NotificationController@get');

  //Projects ROutes
  Route::post('project/add','ProjectController@postProject');
  Route::get('bids/user','BidController@getBidsByUser');
  Route::get('bids/project','BidController@getBidsByProject');
  Route::get('bid','BidController@getBid');
  Route::post('bid','BidController@bid');

  //Blog Posts
  Route::post('post/like','PostController@like');
  Route::post('post/do','PostController@post');
  Route::post('comment','CommentController@comment');

  //Chat ROutes

  Route::get('message/get','MessageController@get');
  Route::post('message/send','MessageController@send');

  //Qubes Routes

  Route::post('qube','QubeController@makeQubes');
  Route::post('qube/like','QubeController@like');
  //Portfolio Routes

  Route::get('portfolios','PortfolioController@getByUser');
  Route::get('portfolio','PortfolioController@getById');
  Route::post('portfolio/add','PortfolioController@add');
});
