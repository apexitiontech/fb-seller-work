<x-app-layout>
    <div class="container mt-5">
        <h2 class="text-center">Uploaded Barcodes</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>File Name</th>
                    <th>Total Rows</th>
                    <th>Uploaded By</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($uploads as $index => $upload)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $upload->file_name }}</td>
                        <td>{{ $upload->total_rows }}</td>
                        <td>{{ $upload->uploaded_by }}</td>
                        <td>{{ $upload->created_at }}</td>
                        <td>
                            <a href="{{ asset($upload->file_path) }}" class="btn btn-primary" download>Download</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
