<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;


class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:3',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        $token = auth()->login($user);

        return response()->json([
            'message' => 'User successfully registered',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function profilePicture(Request $request){

        $validator = Validator::make($request->all(), [
            'photo' => 'required|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->hasFile('photo')) {
            $user = auth()->user();
            $path = $request->photo->store('profile');
            if($user->profile_pic != null){
                $oldphoto=preg_replace("/\/storage/","", $user->profile_pic);
                $tes = Storage::disk('public')->delete($oldphoto);
                $user->profile_pic = null;
            }
            $user->profile_pic = '/storage/'.$path;
            $user->save();
            return response()->json([
                'message' => 'Image successfully uploaded',
                'user' => $user
            ]);
        }
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => "required|string|email|max:100|unique:users,email,".auth()->user()->id.",id",
            'password' => 'string|min:3'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->has('password')){
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return response()->json([
            'message' => 'User successfully updated',
            'user' => $user
        ]);
    }

    public function delete(){
        $user = auth()->user();
        $user->delete();

        return response()->json([
            'message' => 'User successfully deleted'
        ]);
    }
}
