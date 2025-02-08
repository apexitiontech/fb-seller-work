<x-app-layout>
    @include('message')
    <!-- Content -->

    <div class="container-xxl flex-grow-1">
        <h4 class="breadcrumb-wrapper"></h4>

        <div class="row">
            <div class="col-md-12 col-lg-12 mb-2 order-0">
                <div class="card h-100 bg-primary text-white">
                    <div class="card-header">
                        <h1 class="text-white mb-0">Labels History</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-md-12">
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
                            <tbody class="table-border-bottom-0">
                                @forelse ($csv_uploads as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $data->file_name }}</td>
                                        <td>{{ $data->total_rows }}</td>
                                        <td>{{ $data->processed_rows }}</td>
                                        <td>{{ $data->status }}</td>
                                        <td>{{ $data->message }}</td>
                                        <td>{{ $data->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            @if($data->status === 'completed' || ($data->status === 'failed' && $data->message === 'Insufficient funds' && $data->processed_rows > 0))
                                                <a href="{{ route('download', $data->id) }}" target="_blank" class="btn btn-primary">Download</a>
                                            @else
                                                <button class="btn btn-secondary" disabled>Disabled...</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No data found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>