<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Redirect;
use Response;
use File;
use Socialite;
use App\User;
use App\Mail\QRcode;

class SocialController extends Controller
{
    public function redirect ($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback ($provider)
    {
        $getInfo = Socialite::driver($provider)->user();

        $user = $this->createUser($getInfo,$provider);

        $google2fa = app('pragmarx.google2fa');

        // Save the registration data in an array
        $registrationData = $user;

        // Add the secret key to the registration data
        $registrationData['google2fa_secret'] = $google2fa->generateSecretKey();

        Mail::to($to)->send(new QRcode($qr));

        return redirect()->to('/home');

    }

    function createUser ($getInfo,$provider) 
    {

        $user = User::where('provider_id', $getInfo['id'])->first();

        if (!$user) {
            $user = User::create([
            'name'     => $getInfo['name'],
            'email'    => $getInfo['email'],
            'provider' => $provider,
            'provider_id' => $getInfo['id'],
            ]);
        }
        return $user;
    }
}
