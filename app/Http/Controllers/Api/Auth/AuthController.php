<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Api\ApiBaseController as Controller;
use App\Http\Requests\ApiAuthRegisterRequest;
use App\Http\Requests\ApiAuthLoginRequest;
use App\Models\User;

class AuthController extends Controller
{
    protected $providers = [
        'facebook', 'google'
    ];

    public function redirectToProvider($driver)
    {
        if( ! $this->isProviderAllowed($driver) ) {
            return $this->error('', "{$driver} is not currently supported", 404);
        }

        try {
            return $this->success(['url' => Socialite::driver($driver)->stateless()->redirect()->getTargetUrl()], 'Login link generated', 200);
        } catch (Exception $e) {
            return $this->error('', $e->getMessage(), 404);
        }
    }

    public function handleProviderCallback($driver)
    {
        try {
            $user = Socialite::driver($driver)->stateless()->user();
        } catch (Exception $e) {
            return $this->error('', $e->getMessage(), 404);
        }
       
        return empty( $user->email )
            ? $this->error('', "No account data returned from {$driver} provider.")
            : $this->loginOrCreateAccount($user, $driver);
    }

    public function register(ApiAuthRegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'email' => $request->email,
        ]);

        return $this->success($user->only(['name', 'email']), 'User successfully register!');
    }

    public function login(ApiAuthLoginRequest $request)
    {
        $user = $request->validated();
        if (!Auth::attempt(['email' => $user['email'], 'password' => $user['password']])) {
            return $this->error('', 'Credentials not match', 401);
        }

        return $this->success(['token' => auth()->user()->createToken($user['device_name'])->plainTextToken], 'User successfully login!', 202);
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return $this->success('', 'User successfully logout!');
    }

    protected function loginOrCreateAccount($providerUser, $driver)
    {
        $user = User::firstOrCreate([
            'email' => $providerUser->getEmail(),
        ], [
            'name' => $providerUser->getName(),
            'email' => $providerUser->getEmail(),
            'provider' => $driver,
            'provider_id' => $providerUser->getId(),
            'password' => Hash::make(Str::random(10)),
        ]);

        return $this->success(['token' => $user->createToken($driver)->plainTextToken], 'User successfully login!', 202);
    }

    private function isProviderAllowed($driver)
    {
        return in_array($driver, $this->providers) && config()->has("services.{$driver}");
    }
}
