<x-app-layout>
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-6">
            <h4 class="mb-2">Manage Serial Number</h4>
        </div>
        <div class="col-sm-6 col-lg-6 d-flex justify-content-end">
            <button type="button" class="btn btn-primary waves-effect waves-light me-3" data-bs-toggle="modal"
                data-bs-target="#newSerialNumber">Import Serial No.</button>

            <a href="{{ route('serial_number.download.csv') }}" download=""
                class="btn btn-secondary waves-effect waves-light">Sample CSV</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $error }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>


    <div class="row">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="col-md-4">
                    <form action="{{ route('manage-serial-number.index') }}" method="GET" class="p-4">
                        <div class="mb-3">
                            <label for="vendor_id" class="form-label">Filter by Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Vendors</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}"
                                        {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="table-responsive pt-0">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Batch No</th>
                                <th>Serial No</th>
                                <th>Status</th>
                                <th>Vendor Name</th>
                                <th>Uploaded By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($serials as $serial)
                                <tr>
                                    <td>{{ $serial->id }}</td>
                                    <td>{{ $serial->batch_number }}</td>
                                    <td>{{ $serial->serial_number }}</td>
                                    <td>
                                        @if ($serial->is_link)
                                            <span class="badge bg-success">Used</span>
                                        @else
                                            <span class="badge bg-secondary">Unused</span>
                                        @endif
                                    </td>
                                    <td>{{ $serial->vendor_name }}</td>
                                    <td>{{ $serial->uploaded_by }}</td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $serial->id }}">
                                            <i class="me-2 bx bx-trash"></i> Delete
                                        </button>
                                    </td>


                                    {{-- delet modal  --}}
                                    <div class="modal fade" id="deleteModal{{ $serial->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete serial number: <strong>{{ $serial->serial_number }}</strong>?</p>
                                                    <p class="text-danger">This action cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="{{ route('manage-serial-number.destroy', $serial->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </tr>
                            @empty
                            {{-- <tr>No Record found</tr> --}}
                            @endforelse
                        </tbody>
                    </table>
                </div>

               
                <div class="d-flex p-3 justify-content-between mt-3">
                    {{ $serials->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="newSerialNumber" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <form action="{{ route('manage-serial-number.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3>Add New File</h3>
                        </div>
                        <div class="mb-3">
                            <label for="vendor_id" class="form-label">Select Vendor</label>
                            <select class="form-select" name="vendor_id" required>
                                <option value="">Select a vendor</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Upload CSV File</label>
                            <input type="file" class="form-control" name="csv_file" required>
                            <small class="text-muted">CSV must contain a serial_number column</small>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
