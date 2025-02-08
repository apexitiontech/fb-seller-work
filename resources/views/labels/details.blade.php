<x-app-layout>
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
                        <h1 class="text-white mb-0">Upload CSV file </h1>
                    </div>
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
                                    <th>Date/Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0" id="csv-uploads-data-table">
                                {{-- @forelse ($csv_datas as $data)
                                    <tr id="csv-upload-{{ $data->id }}">
                                        <td>{{ $data->id }}</td>
                                        <td>{{ $data->file_name }}</td>
                                        <td class="total-rows">{{ $data->total_rows }}</td>
                                        <td class="processed-rows">{{ $data->processed_rows }}</td>
                                        <td class="status">{{ $data->status }}</td>
                                        <td class="message">{{ $data->message }}</td>
                                        <td>{{ $data->created_at }}</td>
                                        <td>
                                            <a href="{{ asset($data->file_path) }}" download>
                                                <button>Download</button>
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                @endforelse --}}
                            </tbody>
                        </table>
                        <div class="pagiantion">
                            {{ $csv_datas->links() }}
                        </div>
                    </div>
                </div>
                <!--/ Bootstrap Table with Header Dark -->
            </div>
        </div>
    </div>

    {{-- ++++++++++++++++++++++++++++   CARD    +++++++++++++++++++++ --}}


    <!-- / Content -->



    @push('js')
        <script>
            $(document).ready(function() {
                const tableBody = document.getElementById('csv-uploads-data-table');

                // Function to load CSV uploads and populate the table
                async function loadCsvUploads() {
                    try {
                        const response = await fetch('/csv-uploads-data'); // Endpoint to get CSV uploads data
                        const csvUploads = await response.json();

                        // Sort the uploads by created_at in descending order
                        csvUploads.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

                        // Clear existing rows
                        tableBody.innerHTML = '';

                        // Check if there is no data
                        if (csvUploads.length === 0) {
                            // Show "No data found" message
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                                <td colspan="8" class="text-center">No datas found</td>
                                            `;
                            tableBody.appendChild(row);
                            return; // Exit the function if there's no data
                        }

                        // Populate table with fetched and sorted data
                        csvUploads.forEach((data) => {
                            const row = document.createElement('tr');
                            row.id = `csv-upload-${data.id}`;

                            // Format created_at date
                            const createdAt = new Date(data.created_at);
                            const formattedDate = createdAt.toLocaleDateString();
                            const formattedTime = createdAt.toLocaleTimeString();

                            // Conditionally render the button based on the status
                            let downloadButton;
                            if (data.status === 'completed' || (data.status === 'failed' && data.message ===
                                    'Insufficient funds' && data.processed_rows > 0)) {
                                downloadButton =
                                    `<a href="/download/${data.id}" target="_blank" class="btn btn-primary">Download</a>`;
                            } else {
                                downloadButton =
                                    `<button class="btn btn-secondary" disabled>Disabled...</button>`;
                            }

                            row.innerHTML = `
                                                <td>${data.id}</td>
                                                <td>${data.file_name}</td>
                                                <td class="total-rows">${data.total_rows}</td>
                                                <td class="processed-rows">${data.processed_rows}</td>
                                                <td class="status">${data.status}</td>
                                                <td class="message">${data.message}</td>
                                                <td>${formattedDate} ${formattedTime}</td>
                                                <td>${downloadButton}</td>
                                            `;

                            tableBody.appendChild(row);
                        });
                    } catch (error) {
                        console.error('Error loading CSV uploads:', error);
                    }
                }

                // Function to start real-time updates
                function startRealTimeUpdates() {
                    setInterval(() => {
                        loadCsvUploads();
                    }, 2000); // Update every 2 seconds
                }

                // Initial load and start real-time updates
                loadCsvUploads();
                startRealTimeUpdates();
            });
        </script>
    @endpush





</x-app-layout>
