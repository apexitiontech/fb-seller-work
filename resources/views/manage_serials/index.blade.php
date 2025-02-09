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
            @if (session('error'))
                <div>{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between px-3"
                    role="alert">
                    <div><strong>Error !</strong> {{ session('error') }}</div>
                    <button type="button" class="close float-right custom-dismiss-btn" data-bs-dismiss="alert"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div></div>
            @endif

            @if ($errors->any())
                <div>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between px-3"
                                role="alert">
                                <div><strong>Error !</strong> {{ $error }}</div>
                                <button type="button" class="close float-right custom-dismiss-btn"
                                    data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
    <!-- Content -->
    <div class="row">
        <div class="container-xxl flex-grow-1 container-p-y">
            {{-- <h4 class="py-3 breadcrumb-wrapper mb-4"><span class="text-muted fw-light">DataTables /</span> Basic</h4> --}}
            <!-- DataTable with Buttons -->
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-manage-serial-numbers table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>id</th>
                                <th>Batch No</th>
                                <th>Serial No</th>
                                <th>Vendor Name</th>
                                <th>Uploaded By</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="newSerialNumber" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-simple modal-add-new-cc">
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
                            <select class="form-select" name="vendor_id" id="vendor_id" required>
                                <option value="">Select a vendor</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Upload CSV File</label>
                            <input type="file" class="form-control" name="csv_file" id="csv_file" required>
                            <small class="text-muted">CSV must contain a serial_number column</small>
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


    @push('js')
        <script>
            $(document).ready(function() {
                var dt_basic_table = $('.datatables-manage-serial-numbers'),
                    dt_complex_header_table = $('.dt-complex-header'),
                    dt_row_grouping_table = $('.dt-row-grouping'),
                    dt_multilingual_table = $('.dt-multilingual'),
                    dt_basic;

                // DataTable with buttons
                // --------------------------------------------------------------------

                if (dt_basic_table.length) {
                    dt_basic = dt_basic_table.DataTable({
                        ajax: {
                            url: 'get-serial-number',
                            dataSrc: function(json) {
                                console.log(json); // Log the entire response to check its structure
                                return json.data;
                            }
                        },
                        columns: [{
                                data: null
                            }, // For Responsive
                            {
                                data: null
                            }, // For Checkboxes
                            {
                                data: 'id'
                            }, // id
                            {
                                data: 'batch_number'
                            }, // Batch No
                            {
                                data: 'serial_number'
                            }, // Serial No
                            {
                                data: 'vendor_name'
                            }, // Serial No
                            {
                                data: 'uploaded_by'
                            },
                        ],
                        columnDefs: [{
                                className: 'control',
                                orderable: false,
                                searchable: false,
                                responsivePriority: 2,
                                targets: 0,
                                render: function(data, type, full, meta) {
                                    return '';
                                }
                            },
                            {
                                targets: 1,
                                orderable: false,
                                searchable: false,
                                responsivePriority: 3,
                                checkboxes: true,
                                render: function() {
                                    return '<input type="checkbox" class="dt-checkboxes form-check-input">';
                                },
                                checkboxes: {
                                    selectAllRender: '<input type="checkbox" class="form-check-input">'
                                }
                            },
                            {
                                targets: 5,
                                title: 'Vendor Name',
                                orderable: false,
                                searchable: false,
                                render: function(data, type, full, meta) {
                                    return data; // The action buttons are already provided in the data
                                }
                            }
                        ],
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
                                                    if (item.classList !==
                                                        undefined &&
                                                        item.classList.contains(
                                                            'user-name')) {
                                                        result = result + item
                                                            .lastChild
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
                                                    if (item.classList !==
                                                        undefined &&
                                                        item.classList.contains(
                                                            'user-name')) {
                                                        result = result + item
                                                            .lastChild
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
                                                    if (item.classList !==
                                                        undefined &&
                                                        item.classList.contains(
                                                            'user-name')) {
                                                        result = result + item
                                                            .lastChild
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
                                                    if (item.classList !==
                                                        undefined &&
                                                        item.classList.contains(
                                                            'user-name')) {
                                                        result = result + item
                                                            .lastChild
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
                                                    if (item.classList !==
                                                        undefined &&
                                                        item.classList.contains(
                                                            'user-name')) {
                                                        result = result + item
                                                            .lastChild
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
                        }, ],
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
                                            '' ?
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

                                    return data ? $('<table class="table"/><tbody />').append(
                                        data) : false;
                                }
                            }
                        }
                    });
                    $('div.head-label').html('<h5 class="card-title mb-0">Manage Serial Numbers</h5>');
                }
            })
        </script>
    @endpush

</x-app-layout>
