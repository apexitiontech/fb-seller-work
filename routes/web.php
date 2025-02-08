<?php

use App\Http\Controllers\LabelController;
use App\Http\Controllers\ManageSerialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarcodeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Default landing page route
Route::get('/', function () {
    return view('welcome');
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('vendor:publish', [
        '--provider' => 'Milon\Barcode\BarcodeServiceProvider',
        '--force' => true,
    ]);
    return "Cache cleared and Milon/Barcode assets published!";
});


// use DNS1D;
// use DNS2D;

// Route::get('/barcode-test', function () {
//     return DNS1D::getBarcodeHTML('123456789', 'C39'); // Generate a Code39 barcode
// });

// Route::get('/qrcode-test', function () {
//     return DNS2D::getBarcodeHTML('https://example.com', 'QRCODE'); // Generate a QR Code
// });

use Symfony\Component\Process\Process;

// Route::get('/run-composer-update', function () {
//     $homePath = '/home/u664339482';
//     $projectRoot = '/home/u664339482/domains/fbmsellers.info/public_html';

//     $process = new Process(['composer', 'update', '--no-plugins'], $projectRoot);
//     $process->setEnv(['HOME' => $homePath, 'COMPOSER_HOME' => $homePath]);
//     $process->setTimeout(600);

//     $process->run();

//     if (!$process->isSuccessful()) {
//         return response()->json(['status' => 'error', 'message' => $process->getErrorOutput()]);
//     }

//     return response()->json(['status' => 'success', 'message' => 'Composer update completed!']);
// });

use Symfony\Component\Process\Exception\ProcessFailedException;

Route::get('/update-composer', function () {
    $env = [
        'HOME' => '/home/u664339482/domains/fbmsellers.info/public_htmlt',
        'COMPOSER_HOME' => '/home/u664339482/domains/fbmsellers.info/public_html.composer'
    ];

    $process = new Process(['composer', 'update', '--no-plugins'], null, $env);
    $process->run();

    if (!$process->isSuccessful()) {
        return response()->json(['status' => 'error', 'message' => $process->getErrorOutput()]);
    }

    return response()->json(['status' => 'success', 'message' => 'Composer updated successfully!']);
});









// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Create storage link
Route::get('/storage-link', function () {
    $targetFolder = storage_path('app/public');
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/barcodeapp/storage';
    symlink($targetFolder, $linkFolder);
});

Route::middleware('auth')->group(function () {

    // Manage user details - All roles can access these
    Route::middleware(['role:super_admin|admin|user'])->group(function () {
        Route::get('get-user-details', [UserController::class, 'getUserDetails'])->name('get-user-details');
        // Label management (Allowed for all roles)
        Route::resource('labels', LabelController::class);
        Route::post('label-bulk', [LabelController::class, 'bulk_store'])->name('label-bulk.store');
        Route::get('invoice/{id}', [LabelController::class, 'invoice'])->name('invoice');
        Route::get('/label-detail/download-csv', [LabelController::class, 'downloadCsv'])->name('label-detail.download.csv');
        Route::get('/csv-uploads-data', [LabelController::class, 'getCsvUploadsData']);
        Route::get('/download/{uploadId}', [LabelController::class, 'downloadZip'])->name('download.zip');
        Route::get('labels-history', [LabelController::class, 'old_labels_history'])->name('labels-history');
    });

    // ❌ Restricted to super_admin & admin only
    Route::middleware(['role:super_admin|admin'])->group(function () {
        // User management (Restricted)
        Route::resource('users', UserController::class);
        Route::get('get-user-details', [UserController::class, 'getUserDetails'])->name('get-user-details');
        // Serial number management (Restricted)
        Route::get('manage-serial-number', [ManageSerialController::class, 'index'])->name('manage-serial-number.index');
        Route::get('get-serial-number', [ManageSerialController::class, 'getSerials'])->name('get-serial-number');
        Route::post('manage-serial-number', [ManageSerialController::class, 'store'])->name('manage-serial-number.store');
        Route::get('/manage-serial-number/download-csv', [ManageSerialController::class, 'downloadCsv'])->name('serial_number.download.csv');
        Route::get('serial-number-link-to-zero', [ManageSerialController::class, 'serial_number_link_to_zero'])->name('serial-number-link-to-zero');

        // Barcode management (Restricted)
        Route::get('/barcodes/upload', [BarcodeController::class, 'showUploadForm'])->name('barcodes.upload');
        Route::post('/barcodes/process', [BarcodeController::class, 'processBarcodes'])->name('barcodes.process');
        Route::get('/barcodes', [BarcodeController::class, 'getUploadedBarcodes'])->name('barcodes.index');
    });

    // User profile routes - All authenticated users
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});










// Include authentication routes
require __DIR__ . '/auth.php';
