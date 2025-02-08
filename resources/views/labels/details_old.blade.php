<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    {{-- <div class="row mb-4">
        <div class="col-sm-6 col-lg-6">
        </div>
        <div class="col-sm-6 col-lg-6 d-flex justify-content-end">
            <button type="button" class="btn btn-primary waves-effect waves-light me-3" data-bs-toggle="modal"
                data-bs-target="#newUserDetails">Import CSV.</button>

            <a href="{{ route('label-detail.download.csv') }}" download=""
                class="btn btn-secondary waves-effect waves-light  me-3">Sample CSV</a>

            <a href="{{ route('users.create') }}" class="btn btn-info waves-effect waves-light">
                Create
            </a>
        </div>
    </div> --}}
    @include('message')
    <!-- Content -->

    {{-- ++++++++++++++++++++++++++++   CARD    +++++++++++++++++++++ --}}
    <div class="container-xxl flex-grow-1 ">
        <h4 class=" breadcrumb-wrapper">
            {{-- <span class="text-muted fw-light">UI Elements /</span> Cards Advance --}}
        </h4>

        <div class="row">
            <!-- Gamification Card -->
            <div class="col-md-12 col-lg-12 mb-3 order-0">
                <div class="card h-100 bg-primary text-white">
                    <div class="card-header">
                        {{-- <h3 class="card-title mb-2">Congratulations John!</h3> --}}
                        <h1 class="text-white mb-0">Upload CSV file </h1>
                        {{-- <span class="">
                            Upload CSV file to get bulk labels
                        </span> --}}
                    </div>
                    {{-- <div class="card-body">
                    </div> --}}
                </div>
            </div>
            <!--/ Gamification Card -->

            <div class="col-md-12 col-lg-12 mb-2 order-0">
                <div class="card">
                    <h5 class="card-header">Upload CSV File</h5>
                    <div class="card-body mt-4">
                        <form action="{{ route('label-bulk.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="">
                                        <input type="file" class="form-control" name="csv_file" id=""
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary ml-5">Submit</button>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-6">
                                    <div class="mt-2">
                                        <label for="largeSelect" class="form-label">
                                            Choose Vendor (Select any option for USPS Pre-Shipment Labels)
                                        </label>
                                        <select id="largeSelect" class="form-select form-select-lg" name="vendor"
                                            required>
                                            <option value="">Choose a vendor</option>
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor['value'] }}">{{ $vendor['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {{-- <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button> --}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>


        <div class="row mt-5">
            <div class="col-md-12">
                <!-- Bootstrap Table with Header - Dark -->
                <div class="card">
                    <h5 class="card-header">Your Uploads</h5>
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>File</th>
                                    <th>Total Rows</th>
                                    <th>Processed</th>
                                    <th>Status</th>
                                    <th>Message</th>
                                    <th>Tracking Link</th>
                                    <th>Date/Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach ($csv_datas as $index => $data)
                                    <tr id="csv-upload-{{ $data->id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $data->file_name }}</td>
                                        <td class="total-rows">{{ $data->total_rows }}</td>
                                        <td class="processed-rows">{{ $data->processed_rows }}</td>
                                        <td class="status">{{ $data->status }}</td>
                                        <td>{{ $data->message }}</td>
                                        <td>{{ $data->hash }}</td>
                                        <td>{{ $data->created_at }}</td>
                                        <td>
                                            <button>Download</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--/ Bootstrap Table with Header Dark -->
            </div>
        </div>
    </div>

    {{-- ++++++++++++++++++++++++++++   CARD    +++++++++++++++++++++ --}}

    <div class="row">
        <div class="container-xxl flex-grow-1 container-p-y">
            {{-- <h4 class="py-3 breadcrumb-wrapper mb-4"><span class="text-muted fw-light">DataTables /</span> Basic</h4> --}}
            <!-- DataTable with Buttons -->
            {{-- <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-user-details table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice</th>
                                <th>From Name</th>
                                <th>From Company</th>
                                <th>From Phone</th>
                                <th>From Address1</th>
                                <th>From Address2</th>
                                <th>From City</th>
                                <th>From State</th>
                                <th>From Postcode</th>
                                <th>From Country</th>
                                <th>To Name</th>
                                <th>To Company</th>
                                <th>To Phone</th>
                                <th>To Address1</th>
                                <th>To Address2</th>
                                <th>To City</th>
                                <th>To State</th>
                                <th>To Postcode</th>
                                <th>To Country</th>
                                <th>Length</th>
                                <th>Width</th>
                                <th>Height</th>
                                <th>Weight</th>
                                <th>Notes</th>
                                <th>Barcode</th>
                                <th>DataMatrix</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> --}}
            <!-- Modal to add new record -->
            <div class="offcanvas offcanvas-end" id="add-new-record">
                <div class="offcanvas-header border-bottom">
                    <h5 class="offcanvas-title" id="exampleModalLabel">New Record</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body flex-grow-1">
                    <form class="add-new-record pt-0 row g-2" id="form-add-new-record" onsubmit="return false">
                        <div class="col-sm-12">
                            <label class="form-label" for="basicFullname">Full Name</label>
                            <div class="input-group input-group-merge">
                                <span id="basicFullname2" class="input-group-text"><i class="bx bx-user"></i></span>
                                <input type="text" id="basicFullname" class="form-control dt-full-name"
                                    name="basicFullname" placeholder="John Doe" aria-label="John Doe"
                                    aria-describedby="basicFullname2" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label" for="basicPost">Post</label>
                            <div class="input-group input-group-merge">
                                <span id="basicPost2" class="input-group-text"><i class="bx bxs-briefcase"></i></span>
                                <input type="text" id="basicPost" name="basicPost" class="form-control dt-post"
                                    placeholder="Web Developer" aria-label="Web Developer"
                                    aria-describedby="basicPost2" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label" for="basicEmail">Email</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input type="text" id="basicEmail" name="basicEmail" class="form-control dt-email"
                                    placeholder="john.doe@example.com" aria-label="john.doe@example.com" />
                            </div>
                            <div class="form-text">You can use letters, numbers & periods</div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label" for="basicDate">Joining Date</label>
                            <div class="input-group input-group-merge">
                                <span id="basicDate2" class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="text" class="form-control dt-date" id="basicDate" name="basicDate"
                                    aria-describedby="basicDate2" placeholder="MM/DD/YYYY" aria-label="MM/DD/YYYY" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label" for="basicSalary">Salary</label>
                            <div class="input-group input-group-merge">
                                <span id="basicSalary2" class="input-group-text"><i class="bx bx-dollar"></i></span>
                                <input type="number" id="basicSalary" name="basicSalary"
                                    class="form-control dt-salary" placeholder="12000" aria-label="12000"
                                    aria-describedby="basicSalary2" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary data-submit me-sm-3 me-1">Submit</button>
                            <button type="reset" class="btn btn-outline-secondary"
                                data-bs-dismiss="offcanvas">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ DataTable with Buttons -->
        </div>
    </div>
    <!-- / Content -->

    <!-- Add New User Details -->
    <div class="modal fade" id="newUserDetails" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <form action="{{ route('label-bulk.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3>Add New cFile</h3>
                        </div>

                        <div class="fallback">
                            <input type="file" class="form-control" name="csv_file" id="">
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                        <button type="reset" class="btn btn-label-secondary btn-reset" data-bs-dismiss="modal"
                            aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add New User Details -->





    @push('js')
        <script>
            $(document).ready(function() {
                // Create an array to store upload IDs
                const uploadIds = @json($csv_datas->pluck('id'));

                async function checkCsvUploadStatus(uploadId) {
                    try {
                        const response = await fetch(`/csv-upload-status/${uploadId}`);
                        const data = await response.json();

                        console.log(`Status: ${data.status}`);
                        console.log(`Processed Rows: ${data.processed_rows} / ${data.total_rows}`);
                        console.log(`Error: ${data.error_message || 'None'}`);

                        // Update the UI based on the data
                        const row = document.getElementById(`csv-upload-${data.id}`);
                        if (row) {
                            row.querySelector('.processed-rows').textContent = data.processed_rows;
                            row.querySelector('.status').textContent = data.status;
                            // You can also update other fields as needed
                        }
                    } catch (error) {
                        console.error('Error checking CSV upload status:', error);
                    }
                }

                // Check the status for each upload ID
                setInterval(() => {
                    uploadIds.forEach(uploadId => {
                        checkCsvUploadStatus(uploadId);
                    });
                }, 2000); // Check every 5 seconds
            });
        </script>
    @endpush




    {{-- @push('js')
        <script>
            $(document).ready(function() {
                const uploadId = {{ $csvUpload->id }};
                async function checkCsvUploadStatus(uploadId) {
                    try {
                        const response = await fetch(`/csv-upload-status/${uploadId}`);
                        const data = await response.json();

                        console.log(`Status: ${data.status}`);
                        console.log(`Processed Rows: ${data.processed_rows} / ${data.total_rows}`);
                        console.log(`Error: ${data.error_message || 'None'}`);

                        // Update the UI based on the data
                    } catch (error) {
                        console.error('Error checking CSV upload status:', error);
                    }
                }
                setInterval(() => {
                    checkCsvUploadStatus(uploadId); // Pass the uploadId here
                }, 5000); // Check every 5 seconds
            });


            

            var dt_basic_table = $('.datatables-user-details'),
                dt_complex_header_table = $('.dt-complex-header'),
                dt_row_grouping_table = $('.dt-row-grouping'),
                dt_multilingual_table = $('.dt-multilingual'),
                dt_basic;

            // DataTable with buttons
            // --------------------------------------------------------------------

            if (dt_basic_table.length) {
                dt_basic = dt_basic_table.DataTable({
                    ajax: {
                        url: 'get-user-details',
                        dataSrc: function(json) {
                            console.log(json); // Log the entire response to check its structure
                            return json.data;
                        }
                    },
                    columns: [{
                            data: null
                        }, // For # column
                        {
                            data: 'invoice'
                        },
                        {
                            data: 'from_name'
                        }, // From Name
                        {
                            data: 'from_company'
                        }, // From Company
                        {
                            data: 'from_phone'
                        }, // From Phone
                        {
                            data: 'from_address1'
                        }, // From Address1
                        {
                            data: 'from_address2'
                        }, // From Address2
                        {
                            data: 'from_city'
                        }, // From City
                        {
                            data: 'from_state'
                        }, // From State
                        {
                            data: 'from_postcode'
                        }, // From Postcode
                        {
                            data: 'from_country'
                        }, // From Country
                        {
                            data: 'to_name'
                        }, // To Name
                        {
                            data: 'to_company'
                        }, // To Company
                        {
                            data: 'to_phone'
                        }, // To Phone
                        {
                            data: 'to_address1'
                        }, // To Address1
                        {
                            data: 'to_address2'
                        }, // To Address2
                        {
                            data: 'to_city'
                        }, // To City
                        {
                            data: 'to_state'
                        }, // To State
                        {
                            data: 'to_postcode'
                        }, // To Postcode
                        {
                            data: 'to_country'
                        }, // To Country
                        {
                            data: 'length'
                        }, // Length
                        {
                            data: 'width'
                        }, // Width
                        {
                            data: 'height'
                        }, // Height
                        {
                            data: 'weight'
                        }, // Weight
                        {
                            data: 'notes'
                        }, // Notes
                        {
                            data: 'barcode_path_gs128',
                            render: function(data) {
                                return data;
                            }
                        }, // Barcode
                        {
                            data: 'barcode_path_gs1_datamatrix',
                            render: function(data) {
                                return data;
                            }
                        }, // DataMatrix
                        {
                            data: 'action',
                            render: function(data) {
                                return data;
                            }
                        } // Action
                    ],
                    columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 2,
                        render: function(data, type, full, meta) {
                            return meta.row + 1;
                        }
                    }],
                    order: [
                        [2, 'desc']
                    ],
                    dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    displayLength: 7,
                    lengthMenu: [7, 10, 25, 50, 75, 100],
                    buttons: [{
                            extend: 'collection',
                            className: 'btn btn-label-primary dropdown-toggle me-2',
                            text: '<i class="bx bx-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                            buttons: [{
                                    extend: 'print',
                                    text: '<i class="bx bx-printer me-1" ></i>Print',
                                    className: 'dropdown-item',
                                    exportOptions: {
                                        columns: [3, 4, 5, 6, 7],
                                        // prevent avatar to be display
                                        format: {
                                            body: function(inner, coldex, rowdex) {
                                                if (inner.length <= 0) return inner;
                                                var el = $.parseHTML(inner);
                                                var result = '';
                                                $.each(el, function(index, item) {
                                                    if (item.classList !== undefined &&
                                                        item.classList.contains(
                                                            'user-name')) {
                                                        result = result + item.lastChild
                                                            .firstChild.textContent;
                                                    } else if (item.innerText ===
                                                        undefined) {
                                                        result = result + item
                                                            .textContent;
                                                    } else result = result + item
                                                        .innerText;
                                                });
                                                return result;
                                            }
                                        }
                                    },
                                    customize: function(win) {
                                        //customize print view for dark
                                        $(win.document.body)
                                            .css('color', config.colors.headingColor)
                                            .css('border-color', config.colors.borderColor)
                                            .css('background-color', config.colors.bodyBg);
                                        $(win.document.body)
                                            .find('table')
                                            .addClass('compact')
                                            .css('color', 'inherit')
                                            .css('border-color', 'inherit')
                                            .css('background-color', 'inherit');
                                    }
                                },
                                {
                                    extend: 'csv',
                                    text: '<i class="bx bx-file me-1" ></i>Csv',
                                    className: 'dropdown-item',
                                    exportOptions: {
                                        columns: [3, 4, 5, 6, 7],
                                        // prevent avatar to be display
                                        format: {
                                            body: function(inner, coldex, rowdex) {
                                                if (inner.length <= 0) return inner;
                                                var el = $.parseHTML(inner);
                                                var result = '';
                                                $.each(el, function(index, item) {
                                                    if (item.classList !== undefined &&
                                                        item.classList.contains(
                                                            'user-name')) {
                                                        result = result + item.lastChild
                                                            .firstChild.textContent;
                                                    } else if (item.innerText ===
                                                        undefined) {
                                                        result = result + item
                                                            .textContent;
                                                    } else result = result + item
                                                        .innerText;
                                                });
                                                return result;
                                            }
                                        }
                                    }
                                },
                                {
                                    extend: 'excel',
                                    text: '<i class="bx bxs-file-export me-1"></i>Excel',
                                    className: 'dropdown-item',
                                    exportOptions: {
                                        columns: [3, 4, 5, 6, 7],
                                        // prevent avatar to be display
                                        format: {
                                            body: function(inner, coldex, rowdex) {
                                                if (inner.length <= 0) return inner;
                                                var el = $.parseHTML(inner);
                                                var result = '';
                                                $.each(el, function(index, item) {
                                                    if (item.classList !== undefined &&
                                                        item.classList.contains(
                                                            'user-name')) {
                                                        result = result + item.lastChild
                                                            .firstChild.textContent;
                                                    } else if (item.innerText ===
                                                        undefined) {
                                                        result = result + item
                                                            .textContent;
                                                    } else result = result + item
                                                        .innerText;
                                                });
                                                return result;
                                            }
                                        }
                                    }
                                },
                                {
                                    extend: 'pdf',
                                    text: '<i class="bx bxs-file-pdf me-1"></i>Pdf',
                                    className: 'dropdown-item',
                                    exportOptions: {
                                        columns: [3, 4, 5, 6, 7],
                                        // prevent avatar to be display
                                        format: {
                                            body: function(inner, coldex, rowdex) {
                                                if (inner.length <= 0) return inner;
                                                var el = $.parseHTML(inner);
                                                var result = '';
                                                $.each(el, function(index, item) {
                                                    if (item.classList !== undefined &&
                                                        item.classList.contains(
                                                            'user-name')) {
                                                        result = result + item.lastChild
                                                            .firstChild.textContent;
                                                    } else if (item.innerText ===
                                                        undefined) {
                                                        result = result + item
                                                            .textContent;
                                                    } else result = result + item
                                                        .innerText;
                                                });
                                                return result;
                                            }
                                        }
                                    }
                                },
                                {
                                    extend: 'copy',
                                    text: '<i class="bx bx-copy me-1" ></i>Copy',
                                    className: 'dropdown-item',
                                    exportOptions: {
                                        columns: [3, 4, 5, 6, 7],
                                        // prevent avatar to be display
                                        format: {
                                            body: function(inner, coldex, rowdex) {
                                                if (inner.length <= 0) return inner;
                                                var el = $.parseHTML(inner);
                                                var result = '';
                                                $.each(el, function(index, item) {
                                                    if (item.classList !== undefined &&
                                                        item.classList.contains(
                                                            'user-name')) {
                                                        result = result + item.lastChild
                                                            .firstChild.textContent;
                                                    } else if (item.innerText ===
                                                        undefined) {
                                                        result = result + item
                                                            .textContent;
                                                    } else result = result + item
                                                        .innerText;
                                                });
                                                return result;
                                            }
                                        }
                                    }
                                }
                            ]
                        },
                        // {
                        //     text: '<i class="bx bx-plus me-sm-1"></i> <span class="d-none d-sm-inline-block">Add New Record</span>',
                        //     className: 'create-new btn btn-primary'
                        // }
                    ],
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data();
                                    return 'Details of ' + data['full_name'];
                                }
                            }),
                            type: 'column',
                            renderer: function(api, rowIdx, columns) {
                                var data = $.map(columns, function(col, i) {
                                    return col.title !==
                                        '' // ? Do not show row in modal popup if title is blank (for check box)
                                        ?
                                        '<tr data-dt-row="' +
                                        col.rowIndex +
                                        '" data-dt-column="' +
                                        col.columnIndex +
                                        '">' +
                                        '<td>' +
                                        col.title +
                                        ':' +
                                        '</td> ' +
                                        '<td>' +
                                        col.data +
                                        '</td>' +
                                        '</tr>' :
                                        '';
                                }).join('');

                                return data ? $('<table class="table"/><tbody />').append(data) : false;
                            }
                        }
                    }
                });
                $('div.head-label').html('<h5 class="card-title mb-0">Label Details</h5>');
            }
            })
        </script>
    @endpush --}}

</x-app-layout>
