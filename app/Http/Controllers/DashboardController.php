<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\ManageSerial;

class DashboardController extends Controller
{
    public function index()
    { 
        $usedSerialNumbers = ManageSerial::where('is_link', 1)->count();
        $unusedSerialNumbers = ManageSerial::where('is_link', 0)->count();
    
        $vendors = Vendor::with(['serials' => function ($query) {
            $query->select('vendor_id', 'is_link');
        }])->get();
    
        return view('dashboard', compact('usedSerialNumbers', 'unusedSerialNumbers', 'vendors'));
    }
    
}
