<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Socialite;
use Auth;
use Exception;
use App\Models\User;

class GoogleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        try {
    
            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->id)->first();
    
            if($finduser){
                
                $token = auth()->login($finduser);

                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => 'no expired',
                    'user' => auth()->user()
                ]);;
    
            }else{
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'password' => bcrypt('123456dummy')
                ]);
    
                
    
                return response()->json([
                    'message' => 'User successfully registered',
                    'user' => $newUser
                ]);;
            }
    
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
