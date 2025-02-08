<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCsvUpload;
use App\Models\CsvUpload;
use App\Models\Label;
use App\Models\ManageSerial;
use App\Models\User;
use App\Models\UserDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class LabelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = [
            ['value' => 'shippo', 'label' => 'Shippo'],
            ['value' => 'easyship', 'label' => 'EasyShip'],
            ['value' => 'no_logo', 'label' => 'USPS (No Logo)'],
            ['value' => 'rollo', 'label' => 'Rollo'],
            ['value' => 'shoppify', 'label' => 'Shoppify'],
            ['value' => 'evs', 'label' => 'EVS'],
            ['value' => 'atfm', 'label' => 'ATFM'],
            ['value' => 'easypost', 'label' => 'Easypost'],
            ['value' => 'pitney', 'label' => 'Pitney Bowes'],
            ['value' => 'shipponew', 'label' => 'Shippo (New)'],
        ];

        $users = User::with('roles')->get(); // Eager load roles for each user
        $csv_datas = CsvUpload::orderBy('created_at', 'desc')->paginate(20);
        // dd($users); // Uncomment to debug and view the output
        return view('labels.details', compact('users', 'vendors', 'csv_datas'));
    }

    public function old_labels_history()
    {
        $users = User::with('roles')->get(); // Eager load roles for each user
        $csv_datas = CsvUpload::orderBy('created_at', 'desc')->get();
        // dd($users); // Uncomment to debug and view the output
        return view('labels.history', compact('users', 'csv_datas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = [];
        return view('labels.create', compact('roles'));
    }

  
    public function bulk_store(Request $request)
    {
        // Validate the CSV file and vendor selection
        $request->validate([
            'csv_file' => 'required|mimes:csv',
            'vendor' => 'required|string', // Ensure vendor is provided
        ]);

        $vendor = $request->input('vendor');
        $available_serial_numbers = ManageSerial::where('is_link', 0)->count();

        // Check if there are available serial numbers
        if ($available_serial_numbers === 0) {
            $message = 'No available serial numbers in the database.';
            return back()->with('error', $message);
        }

        $hash = Str::random(16); // Generate a unique hash

        // Define the upload path
        $upload_path = public_path('uploads/' . $hash);

        // Ensure the uploads directory exists
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        // Move the file to the public/uploads directory
        $file = $request->file('csv_file');
        $extension = $file->getClientOriginalExtension(); // Get the original file extension
        $file_name = "{$hash}.{$extension}"; // Include the hash in the filename with extension
        $file_path = "{$upload_path}/{$file_name}"; // Set the new file path

        // Use try-catch to handle file move errors
        try {
            $file->move($upload_path, $file_name);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload file: ' . $e->getMessage());
        }

        // Count the number of rows in the CSV file
        $file_handle = fopen($file_path, 'r');
        $total_rows = 0;

        // Skip the header row
        fgetcsv($file_handle);

        // Count rows
        while (fgetcsv($file_handle)) {
            $total_rows++;
        }
        fclose($file_handle);

        // Create a record in csv_uploads with the vendor data
        $csv_upload = CsvUpload::create([
            'file_name' => $file_name,
            'file_path' => $file_path,
            'total_rows' => $total_rows,
            'vendor' => $vendor, // Store vendor data here
            'hash' => $hash, // Store the generated hash in the database
            'uploaded_by' => Auth::user()->id,
        ]);

         
        // Dispatch the job with the file path and vendor information
        ProcessCsvUpload::dispatch($file_path, $csv_upload);
 
        // Check if total rows exceed available serial numbers
        if ($total_rows > $available_serial_numbers) {
            $message = "Only {$available_serial_numbers} user details were processed due to limited serial numbers. Please be patient while we handle the file.";
            return back()->with('warning', $message);
        } else {
            return back()->with('success', 'CSV file uploaded successfully. Processing in the background.');
        }
    }




    public function getCsvUploadsData(Request $request)
    {
        $data = $request->input('data');
        $csvUploadsData = [];
        if ($data === 'history') {
            // Fetch records excluding those created today
            $csvUploadsData = CsvUpload::whereDate('created_at', '<', now()->startOfDay())
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Fetch records created today (or handle other cases as needed)
            $csvUploadsData = CsvUpload::whereDate('created_at', now()->toDateString())
                ->orderBy('created_at', 'desc')
                ->get();
        }
        return response()->json($csvUploadsData);
    }



    public function downloadZip($uploadId)
    {
        $csvUpload = CsvUpload::find($uploadId);
        if (!$csvUpload) {
            return response()->json(['message' => 'File not found.'], 404);
        }
        $path_hash = $csvUpload->hash;

        $zipFilePath = public_path("uploads/{$path_hash}/{$path_hash}.zip");

        if (!file_exists($zipFilePath)) {
            return response()->json(['message' => 'ZIP file not found.'], 404);
        }

        return Response::download($zipFilePath);
    }


    public function invoice($id)
    {
        $invoice = UserDetail::find($id);
        // return view('user-details.invoice', compact('invoice'));
        $customPaper = array(0, 0, 420.00, 270.10);
        $pdf = Pdf::loadView('user-details.invoice', compact('invoice'))->setPaper($customPaper, 'landscape');
        return $pdf->download('document.pdf');
    }
    public function downloadCsv()
    {
        $file_path = public_path('assets/csv/user_details.csv');

        if (!file_exists($file_path)) {
            abort(404, 'File not found.');
        }

        return response()->download($file_path, 'user_details.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
    
    // ===========================
    // sadam lable wrokign
    // ===========================
    
    public function showUploadForm()
    {
        return view('labels.upload');
    }

    public function processLabels(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        // Read the CSV file
        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));

        // Extract the header and rows
        $header = array_map('trim', $csvData[0]);
        $rows = array_slice($csvData, 1);

        // Map the data to an associative array
        $labels = [];
        foreach ($rows as $row) {
            $labels[] = array_combine($header, $row);
        }

        // Create a folder to store the labels
        $folderPath = storage_path('app/public/labels');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        // Generate PDF labels
        foreach ($labels as $index => $label) {
            $pdf = Pdf::loadView('labels.pdf', compact('label'));
            $pdf->save($folderPath . "/label_{$index}.pdf");
        }

        // Create a ZIP file
        $zip = new ZipArchive;
        $zipPath = storage_path('app/public/labels.zip');
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = glob($folderPath . '/*.pdf');
            foreach ($files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Clean up individual PDFs (optional)
        array_map('unlink', glob("$folderPath/*"));
        rmdir($folderPath);

        // Provide the ZIP file for download
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
