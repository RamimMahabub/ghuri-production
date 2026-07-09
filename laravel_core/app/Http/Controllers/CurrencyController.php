<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function setCurrency(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|in:BDT,USD',
        ]);

        session(['currency' => $request->currency]);

        return back();
    }
}
