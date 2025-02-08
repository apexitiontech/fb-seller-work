<?php

namespace App\Http\Controllers;

use App\Models\ManageSerial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class ManageSerialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serial = ManageSerial::all();
        return view('manage_serials.index', compact('serial'));
    }

    public function getSerials(Request $request)
    {
        if ($request->ajax()) {
            $data = ManageSerial::select(['manage_serials.id', 'batch_number', 'serial_number', 'uploaded_by'])
                ->join('users', 'manage_serials.uploaded_by', '=', 'users.id')
                ->select('manage_serials.id', 'batch_number', 'serial_number', 'users.name as uploaded_by'); // Assuming 'name' is the column you want from the User model

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');

        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== FALSE) {
            $data = array_combine($header, $row);
            $serialNumber = $data['serial_number'];
            $insert_data = [
                'batch_number' => $serialNumber,
                'serial_number' => $serialNumber,
                'uploaded_by' => Auth::user()->id,
            ];

            // Save or update the data
            ManageSerial::updateOrCreate(['serial_number' => $serialNumber], $insert_data);
        }

        fclose($file);
        return back()->with('success', 'CSV file uploaded successfully.');
    }


    public function downloadCsv()
    {
        $filePath = public_path('assets/csv/serial_number.csv');

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, 'serial_number.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }


    public function serial_number_link_to_zero()
    {
        // ManageSerial::where('is_link', '0')->delete();
        ManageSerial::where('is_link', '1')->update(['is_link' => '0']);
        // return back()->with('success', 'Serial numbers updated successfully.');
        // return 'hi';
    }
}
