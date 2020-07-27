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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

class SocialController extends Controller
{
    public function redirect ($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback ($provider)
    {
        $google2fa = app('pragmarx.google2fa');
        $getInfo = Socialite::driver($provider)->user();
        $user = User::where('provider_id' ,'=',$getInfo['id'])->first();
        if ($user === null) {
            $secret_key = $google2fa->generateSecretKey();
            $user = $this->createUser($getInfo,$provider,$secret_key);
            // Add the secret key to the registration data




            $qr = $google2fa->getQRCodeInline(
                config('app.name'),
                $user['email'],
                $user['google2fa_secret']
            );



            $data = [
                    'qr' => $qr,
                    'secret' => $user['google2fa_secret'],
            ];

            Mail::to($user)->send(new QRcode($data));

            Auth()->login($user);
        }
        Auth()->login($user);
        return redirect()->to('/home');

    }

    function createUser ($getInfo,$provider,$secret_key) 
    {

        $user = User::where('provider_id', $getInfo['id'])->first();

        if (!$user) {
            $user = User::create([
            'name'     => $getInfo['name'],
            'email'    => $getInfo['email'],
            'provider' => $provider,
            'provider_id' => $getInfo['id'],
            'google2fa_secret' => $secret_key,
            ]);
        }
        return $user;
    }
}
