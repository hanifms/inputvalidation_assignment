<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogHttpRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Log the request
        Log::info('Request Method: ' . $request->method());
        Log::info('Request Path: ' . $request->path());
        Log::info('Request GET Parameters: ', $request->query());

        if ($request->isMethod('post')) {
            Log::info('Request POST Data: ', $request->post());
            Log::info('Request has file uploads: ' . ($request->hasFile('*') ? 'Yes' : 'No'));

            // Check if the request is coming from the registration form
            if ($request->is('register')) {
                Log::info('Registration form data:', [
                    'name' => $request->input('name'),
                    'nickname' => $request->input('nickname'),
                    'email' => $request->input('email'),
                    'password_length' => $request->input('password') ? strlen($request->input('password')) : 0,
                    'password_confirmation_length' => $request->input('password_confirmation') ? strlen($request->input('password_confirmation')) : 0,
                ]);
            }
        }

        // Execute the request
        $response = $next($request);

        return $response;
    }
}
