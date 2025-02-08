<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\ManageSerial;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ManageSerialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serial = ManageSerial::all();
        $vendors = Vendor::where('status', 1)->get();
        return view('manage_serials.index', compact('serial', 'vendors'));
    }

    // public function getSerials(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $data = ManageSerial::select(['manage_serials.id', 'batch_number', 'serial_number', 'uploaded_by'])
    //             ->join('users', 'manage_serials.uploaded_by', '=', 'users.id')
    //             ->select('manage_serials.id', 'batch_number', 'serial_number', 'users.name as uploaded_by');

    //         return DataTables::of($data)
    //             ->addIndexColumn()
    //             ->make(true);
    //     }
    // }

    public function getSerials(Request $request)
    {
        if ($request->ajax()) {
            $data = ManageSerial::select([
                'manage_serials.id',
                'manage_serials.batch_number',
                'manage_serials.serial_number',
                'manage_serials.is_link',
                'vendors.name as vendor_name',
                'users.name as uploaded_by'
            ])
                ->join('users', 'manage_serials.uploaded_by', '=', 'users.id')
                ->join('vendors', 'manage_serials.vendor_id', '=', 'vendors.id');

            return DataTables::of($data)
                ->addColumn('status', function ($row) {
                    return $row->is_link ? 'Linked' : 'Not Linked';
                })
                ->addIndexColumn()
                ->make(true);
        }
    }


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'csv_file' => 'required|mimes:csv,txt',
    //         'vendor_id'=> 'required|exists:vendors',
    //     ]);

    //     $path = $request->file('csv_file')->getRealPath();
    //     $file = fopen($path, 'r');

    //     $header = fgetcsv($file);

    //     while (($row = fgetcsv($file)) !== FALSE) {
    //         $data = array_combine($header, $row);
    //         $serialNumber = $data['serial_number'];
    //         $insert_data = [
    //             'batch_number' => $serialNumber,
    //             'serial_number' => $serialNumber,
    //             'uploaded_by' => Auth::user()->id,
    //         ];

    //         // Save or update the data
    //         ManageSerial::updateOrCreate(['serial_number' => $serialNumber], $insert_data);
    //     }

    //     fclose($file);
    //     return back()->with('success', 'CSV file uploaded successfully.');
    // }
    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $file = fopen($path, 'r');
        $header = fgetcsv($file);

        if (!in_array('serial_number', $header)) {
            fclose($file);
            Log::info('CSV file must contain a serial_number column');
            return back()->with('error', 'CSV file must contain a serial_number column');
        }

        $duplicates = [];
        $inserted = 0;
        $rows = [];

        try {
            DB::beginTransaction();

            $batchPrefix = now()->format('Ymd_His_');
            $counter = 0;

            while (($row = fgetcsv($file)) !== FALSE) {
                if (empty($row)) continue; 

                while (count($row) < count($header)) {
                    $row[] = '';
                }

                $data = array_combine($header, $row);

                if (empty($data['serial_number'])) continue; 

                $serialNumber = mb_convert_encoding(trim($data['serial_number']), 'UTF-8', 'auto');


                $exists = ManageSerial::where('vendor_id', $request->vendor_id)
                    ->where('serial_number', $serialNumber)
                    ->exists();

                if (!$exists) {
                    $batchNumber = $batchPrefix . str_pad($counter++, 5, '0', STR_PAD_LEFT);

                    $rows[] = [
                        'vendor_id' => $request->vendor_id,
                        'batch_number' => $batchNumber,
                        'serial_number' => $serialNumber,
                        'is_link' => false,
                        'uploaded_by' => Auth::id(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    if (count($rows) >= 100) {
                        ManageSerial::insert($rows);
                        $inserted += count($rows);
                        $rows = [];
                    }
                } else {
                    $duplicates[] = $serialNumber;
                }
            }

            if (!empty($rows)) {
                ManageSerial::insert($rows);
                $inserted += count($rows);
            }

            DB::commit();
            fclose($file);


            $message = $inserted . " serial numbers imported successfully.";
            if (count($duplicates) > 0) {
                $message .= " " . count($duplicates) . " duplicates were skipped.";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($file);
            return back()->with('error', 'An error occurred while importing the file: ' . $e->getMessage());
        }
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
