<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Intervention\Image\Facades\Image as Image;
use Carbon\Carbon;
use App\UserReviews;

class UserController extends Controller
{
    /*

    Get User data using user data

    */

    public function getUser(Request $request)
    {
        $validator = \Validator::make(
        array(
            'id' => $request->id,
        ),
        array(
            'id' => 'required|exists:users,id',
        ));

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }

        $id = $request->id;

        $user = User::where('id', $id)->first();
        $extra_data = array('followers' => $user->followers(), 'following'=> $user->following(), 'projects_count' => $user->projects_count());
        $user_data = array();
        array_push($user_data,$user);
        array_push($user_data,$extra_data);

        return response()->json(['success' => true, 'data' => $user_data, 'status' => 200]);

    }

    public function partialSignup(Request $request)
    {
        $validator = \Validator::make(
        array(
            'email' => $request->email,
            'password' => $request->password,
            ),
            array(
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            ));

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }

        $credentials = $request->only('email','password');
        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->verified = false;
        $user->save();

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(["success" => false,"error" => "invalid_credentials","status" => 401]);
            }
        } catch (JWTException $e) {
            return response()->json(["success" => false,"error" => "couldnt_create_token","status" => 500]);

        }

        return response()->json(['success' => true, 'data' => compact('token'), 'status' => 200]);
    }

    public function completeAccount(Request $request)
    {
        $id = JWTAuth::parseToken()->authenticate()->id;
        $updateArray = array();

        if ($request->has('name')) {
            $updateArray['name'] = $request->name;
        }

        if ($request->has('email')) {
            $updateArray['email'] = $request->name;
            $updateArray['verified'] = false;

        }


        if ($request->has('password')) {
            $updateArray['password'] = $request->password;
        }

        if ($request->has('gender')) {
            $updateArray['gender'] = $request->gender;
        }

        if ($request->has('gender')) {
            $updateArray['profession'] = $request->profession;
        }

        if ($request->has('firm_name')) {
            $updateArray['firm_name'] = $request->firm_name;
        }

        if ($request->has('addrss')) {
            $updateArray['address'] = $request->address;
        }

        if ($request->has('city')) {
            $updateArray['city'] = $request->city;
        }

        if ($request->has('country')) {
            $updateArray['country'] = $request->country;
        }

        if ($request->has('state')) {
            $updateArray['state'] = $request->state;
        }

        if ($request->has('website')) {
            $updateArray['website'] = $request->website;
        }

        if ($request->has('lat')) {
            $updateArray['lat'] = $request->lat;
        }

        if ($request->has('long')) {
            $updateArray['long'] = $request->long;
        }

        if ($request->hasFile('image')) {
            $file_name = rand(10000, 1000000000).'_'.time();
            $path = public_path('images/users/'.$file_name.'.jpg');
            $url = url('images/users/'.$file_name.'.jpg');
            $file = $request->file('image');
            $img = Image::make($file->getRealPath())->resize(500, 500);
            $img->save($path);
            $updateArray['image'] = $url;
        }

        User::where('id', $id)->update($updateArray);

        return response()->json(['success' => true, 'data' => 'updated', 'status' => 200]);
    }

    public function getNearUsers()
    {
       /* $validator = \Validator::make(
        array(
            'lat' => $request->lat,
            'long' => $request->long,
        ),
        array(
            'lat' => 'required',
            'long' => 'required',
        ));

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }
        
        $lat_user = $request->lat;
        $long_user = $request->long;*/
        
        $user = JWTAuth::parseToken()->authenticate();
        $lat_user = $user->lat;
        $long_user = $user->long;
        
        $near_lats = array($lat_user - 4, $lat_user + 4);
        $near_longs = array($long_user - 4, $long_user + 4);
        $users = User::where('id','!=',$user->id)->get();
        $users_near = array();
        foreach ($users as $u) {
            if ($near_lats[0] < $u->lat && $near_lats[1] > $u->lat) {
                if ($near_longs[0] < $u->long && $near_longs[1] > $u->long) {
                    array_push($users_near, $u);
                }
            }
        }

        return response()->json(['success' => true, 'data' => $users_near, 'status' => 200]);
    }

    public function reset(Request $request)
    {
        $validator = \Validator::make(
            array(
            'email' => $request->email,
            ),
            array(
            'email' => 'email|required|exists:users,email',
            )
            );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }
        $token = rand(100000, 9999999);
        \DB::table('password_resets')->insert(array(
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
            ));
        try {
            mail(
                $to = $request->email,
                $message = $token,
                $subject = 'Password Reset'
                );

            return response()->json(['success' => true, 'data' => 'email sent', 'status' => 200]);
        } catch (Expection $e) {
            return response()->json(['success' => false, 'error' => array("problem sending token"), 'status' => 501]);
        }
    }

    public function resetConfirm(Request $request)
    {
        $validator = \Validator::make(
            array(
            'email' => $request->email,
            'password' => $request->password,
            'code' => $request->code,
            ),
            array(
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8',
            'code' => 'required|min:6',
            ));

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }

        $reset_details = \DB::table('password_resets')->where('email', $request->email)->orderBy('created_at', 'desc')->first();

        if (!empty($reset_details) && $reset_details->token == $request->code) {
            User::where('email', $request->email)->update(array(
                'password' => bcrypt($request->password),
                ));

            try {
                if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                    return response()->json(['error' => 'invalid_credentials'], 401);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
            \DB::table('password_resets')->where('email', $request->email)->orderBy('created_at', 'desc')->delete();

            return response()->json(['success' => true, 'data' => compact('token'), 'status' => 200]);
        }

        return response()->json(['success' => false, 'error' => 'invalid data provided', 'status' => 400]);
    }

    public function follow(Request $request)
    {
        $validator = \Validator::make(
            array(
            'id' => $request->id,
            ),
            array(
            'id' => 'required|exists:users,id',
            ));

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }
        $my_id = JWTAuth::authenticate()->id;

        \DB::table('follows')->insert(array(
            'follower' => $my_id,
            'followed' => $request->id,
            ));

        return response()->json(['success' => true, 'data' => 'followed', 'status' => 200]);
    }

    public function followers(Request $request)
    {
        $validator = \Validator::make(
            array(
            'id' => $request->id,
            ),
            array(
            'id' => 'required|exists:users,id',
            ));

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }

        $followers = User::where('id', $request->id)->first()->followers();

        return response()->json(['success' => true, 'data' => $followers, 'status' => 200]);
    }

    public function followed(Request $request)
    {
        $validator = \Validator::make(
            array(
            'id' => $request->id,
            ),
            array(
            'id' => 'required|exists:users,id',
            ));

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }

        $followed = User::where('id', $request->id)->first()->followed();

        return response()->json(['success' => true, 'data' => $followed, 'status' => 200]);
    }

    public function review(Request $request)
    {
        $validator = \Validator::make(
            array(
            'user_id' => $request->user_id,
            'reviewer_id' => $requst->reviewer_id,
            'rating' => $requst->rating,
            'review' => $request->review,
            ),
            array(
            'user_id' => 'required|exists:users,id',
            'reviewer_id' => 'required|exists:users,id',
            'rating' => 'required|numeric',
            'review' => 'required',
            ));

        if ($validator->fails()) {
            return response()->json([$validator->messages()]);
        }

        $ur = new UserReviews();
        $ur->user_id = $request->user_id;
        $ur->reviewer_id = $request->reviewer_id;
        $ur->rating = $request->rating;
        $ur->review = $request->review;
        $ur->save();

        return response()->json(['success' => true, 'data' => 'reviewed', 'status' => 200]);
    }

    public function getReviews(Request $request)
    {
        $validator = \Validator::make(
            array(
            'id' => $request->id,
            ),
            array(
            'id' => 'required|exists:users,id',
            ));

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages(), 'status' => 400]);
        }

        $user_reviews = UserReviews::where('user_id', $request->id)->all();

        return response()->json(['success' => true, 'data' => $user_reviews, 'status' => 200]);
    }
}
