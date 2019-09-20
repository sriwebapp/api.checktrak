<?php

namespace App\Http\Controllers;

use App\User;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required|min:6',
            'company_id' => 'required|exists:companies,id'
        ]);

        $user = User::where('active', 1)
            ->where( function($query) use ($request) {
                $query->where('email', $request->get('username'))
                    ->orWhere('username', $request->get('username'));
            })->first();

        if(! $user) {
            return response()->json([
                'message' => 'Your credentials are incorrect. Please try again.',
                'errors' => [ 'username' => ['Your credentials are incorrect. Please try again.'] ]
            ], 422);
        }

        $http = new \GuzzleHttp\Client;

        try {
            $response = $http->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.id'),
                    'client_secret' => config('services.passport.secret'),
                    'username' => $user->email,
                    'password' => $request->get('password'),
                ],
            ]);

            Log::info($user->name . ' signed in.');

            return [
                'token' => json_decode($response->getBody(), true),
                'company' => Company::find($request->get('company_id'))->id,
            ];
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            $message;

            if ($e->getCode() === 400) {
                $message = 'Invalid Request. Please enter a username or a password.';
            } else if ($e->getCode() === 401) {
                $message = 'Your credentials are incorrect. Please try again.';
            } else {
                $message = 'Something went wrong on the server.';
            }
            return response()->json([
                    'message' => $message,
                    'errors' => [ 'username' => [$message] ]
                ], 422);
        }
    }

    public function logout(Request $request)
    {
        Log::info($request->user()->name . ' signed out.');

        $request->user()->tokens()->delete();
    }

    public function user()
    {
        return Auth::user()->accessibility();
    }
}
