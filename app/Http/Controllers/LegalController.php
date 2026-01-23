<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Show the Terms and Conditions page.
     */
    public function terms()
    {
        return view('legal.terms');
    }

    /**
     * Show the Privacy Policy page.
     */
    public function privacy()
    {
        return view('legal.privacy');
    }
}