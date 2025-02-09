<?php

namespace App\Http\Controllers;

use App\Models\ManageSerial;

class DashboardController extends Controller
{
    public function index()
    {
        $usedSerialNumbers = ManageSerial::where('is_link', 1)->count();
        $unusedSerialNumbers = ManageSerial::where('is_link', 0)->count();

        return view('dashboard', compact('usedSerialNumbers', 'unusedSerialNumbers'));
    }
}
