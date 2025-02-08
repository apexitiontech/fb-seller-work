<?php

namespace App\Http\Controllers;

use App\Helpers\BarcodeHelper;
use App\Jobs\ProcessCsvUpload;
use App\Models\ManageSerial;
use App\Models\User;
use App\Models\UserDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get(); // Eager load roles for each user
        // dd($users); // Uncomment to debug and view the output
        return view('user-details.index', compact('users'));
    }


    public function create()
    {
        $roles = Role::all();
        return view('user-details.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // return $request->wallet_amount;
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'role_id' => ['required'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'wallet_amount' => $request->wallet_amount ? $request->wallet_amount : 0,
            'per_row_amount' => $request->per_row_amount ? $request->per_row_amount : 0,
        ]);

        $role = Role::findOrFail($request->role_id);
        $user->syncRoles([$role->id]); // Syncs the role by ID
        // event(new Registered($user));

        // Auth::login($user);

        return redirect(route('users.index'))->with('success', 'User inserted successfully');
    }

    public function edit(User $user)
    {

        $roles = Role::all();
        // return $user;
        return view('user-details.edit', compact(['user', 'roles']));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class . ',email,' . $id],
            'role_id' => ['required'],
            'password' => ['nullable', Rules\Password::defaults()],
            'wallet_amount' => ['nullable', 'numeric', 'min:0'], // Validation for wallet_amount
            'per_row_amount' => ['nullable', 'numeric', 'min:0'], // Validation for wallet_amount
        ]);

        $user = User::findOrFail($id);

        // Update user details
        $user->name = $request->name;
        $user->email = $request->email;
        $user->wallet_amount = $request->wallet_amount ?? 0; // Update wallet_amount, default to 0 if null
        $user->per_row_amount = $request->per_row_amount ?? 0; // Update per_row_amount, default to 0 if null

        // Update password only if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update role
        $role = Role::findOrFail($request->role_id);
        $user->syncRoles([$role->id]);

        return redirect(route('users.index'))->with('success', 'User updated successfully');
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect(route('users.index'))->with('success', 'User deleted successfully');
    }

    public function getUserDetails(Request $request)
    {
        if ($request->ajax()) {
            // Select all the columns needed for the DataTable
            $data = UserDetail::select([
                'id',
                'from_name',
                'from_company',
                'from_phone',
                'from_address1',
                'from_address2',
                'from_city',
                'from_state',
                'from_postcode',
                'from_country',
                'to_name',
                'to_company',
                'to_phone',
                'to_address1',
                'to_address2',
                'to_city',
                'to_state',
                'to_postcode',
                'to_country',
                'length',
                'width',
                'height',
                'weight',
                'notes',
                'barcode_path_gs128',   // Barcode image path
                'barcode_path_gs1_datamatrix' // DataMatrix image path
            ]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('invoice', function ($row) {
                    // Generate Print button with the appropriate route
                    $printUrl = route('invoice', $row->id); // Assuming route is named 'invoice'
                    $btn = '<a href="' . $printUrl . '" class="btn btn-primary btn-sm" target="_blank">Invoice</a>';
                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    // Generate Print button with the appropriate route
                    $btn = '<a href="" class="btn btn-danger btn-sm" >Delete</a>
                    <a href="" class="btn btn-primary btn-sm" >Edit</a>';
                    return $btn;
                })
                ->editColumn('barcode_path_gs128', function ($row) {
                    // Render barcode image
                    return '<img src="' . asset($row->barcode_path_gs128) . '" width="100">';
                })
                ->editColumn('barcode_path_gs1_datamatrix', function ($row) {
                    // Render DataMatrix image
                    return '<img src="' . asset($row->barcode_path_gs1_datamatrix) . '" width="100">';
                })
                ->rawColumns(['invoice', 'action', 'barcode_path_gs128', 'barcode_path_gs1_datamatrix'])
                ->make(true);
        }
    }


    public function show()
    {
        $barcodes = BarcodeHelper::generateAndStoreBarcodes('0101234567890128TEC-IT');
    }
}
