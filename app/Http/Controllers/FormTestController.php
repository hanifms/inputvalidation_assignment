<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FormTestController extends Controller
{
    /**
     * Show a test form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        return view('form-test');
    }

    /**
     * Process the test form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processForm(Request $request)
    {
        Log::info('Form test submission received with data:', $request->all());

        $request->validate([
            'test_name' => 'required',
            'test_email' => 'required|email',
        ]);

        return redirect()
            ->back()
            ->with('status', 'Form submitted successfully with data: Name = ' . $request->test_name . ', Email = ' . $request->test_email);
    }
}
