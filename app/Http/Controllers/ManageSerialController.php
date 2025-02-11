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

    public function index(Request $request)
    {
        $query = ManageSerial::select([
            'manage_serials.id',
            'manage_serials.batch_number',
            'manage_serials.serial_number',
            'manage_serials.is_link',
            'vendors.name as vendor_name',
            'users.name as uploaded_by'
        ])
            ->join('users', 'manage_serials.uploaded_by', '=', 'users.id')
            ->join('vendors', 'manage_serials.vendor_id', '=', 'vendors.id');

        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
            $query->where('manage_serials.vendor_id', $request->vendor_id);
        }

        $serials = $query->paginate(10);
        $vendors = Vendor::where('status', 1)->get();

        return view('manage_serials.index', compact('serials', 'vendors'));
    }
    public function destroy($id)
    {
        try {
            $serial = ManageSerial::findOrFail($id);
            $serial->delete();
            return redirect()->back()->with('success', 'Serial number deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting serial number');
        }
    }
    public function deleteAll()
    {
        try {
    
            

            DB::table('manage_serials')->truncate();
            
            
            return redirect()->back()->with('success', 'All serial numbers deleted successfully');
        } catch (\Exception $e) {
            Log::error('Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting serial numbers: ' . $e->getMessage());
        }
    }

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

            // Add vendor filter
            if ($request->has('vendor_id') && !empty($request->vendor_id)) {
                $data->where('manage_serials.vendor_id', $request->vendor_id);
            }

            return DataTables::of($data)
                ->addColumn('status', function ($row) {
                    return $row->is_link ? 'Used' : 'Unused';
                })
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimetypes:text/csv,text/plain',
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

        $allSerialNumbers = [];
        $validRows = [];
        $skippedDuplicates = [];

        while (($row = fgetcsv($file)) !== FALSE) {
            if (empty($row)) continue;

            while (count($row) < count($header)) {
                $row[] = '';
            }

            $data = array_combine($header, $row);
            if (empty($data['serial_number'])) continue;

            $serialNumber = mb_convert_encoding(trim($data['serial_number']), 'UTF-8', 'auto');

            if (in_array($serialNumber, $allSerialNumbers)) {
                $skippedDuplicates[] = $serialNumber;
                continue;
            }

            $allSerialNumbers[] = $serialNumber;
            $validRows[] = ['data' => $data, 'row' => $row];
        }

        $existingSerials = ManageSerial::whereIn('serial_number', $allSerialNumbers)
            ->pluck('serial_number')
            ->toArray();

        if (empty($allSerialNumbers)) {
            fclose($file);
            return back()->with('error', 'No valid serial numbers found in the CSV file.');
        }

        if (count($existingSerials) === count($allSerialNumbers)) {
            fclose($file);
            return back()->with('error', 'All serial numbers in the file already exist in the database');
        }

        $validRows = array_filter($validRows, function ($valid) use ($existingSerials) {
            return !in_array(
                mb_convert_encoding(trim($valid['data']['serial_number']), 'UTF-8', 'auto'),
                $existingSerials
            );
        });

        $inserted = 0;
        $rows = [];

        try {
            DB::beginTransaction();
            $batchPrefix = now()->format('Ymd_His_');
            $counter = 0;

            foreach ($validRows as $valid) {
                $serialNumber = mb_convert_encoding(trim($valid['data']['serial_number']), 'UTF-8', 'auto');

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

                if (count($rows) >= 50) {
                    ManageSerial::insert($rows);
                    $inserted += count($rows);
                    $rows = [];
                }
            }

            if (!empty($rows)) {
                ManageSerial::insert($rows);
                $inserted += count($rows);
            }

            DB::commit();
            fclose($file);

            if ($inserted === 0) {
                return back()->with('error', 'No serial numbers were imported.');
            }

            $message = $inserted . " serial numbers imported successfully.";
            if (!empty($skippedDuplicates)) {
                $message .= " Skipped duplicate entries within file: ";
            }
            if (!empty($existingSerials)) {
                $message .= " Skipped existing entries in database";
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
