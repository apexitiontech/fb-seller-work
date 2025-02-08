<x-app-layout>
    <div class="container mt-5">
        <h1 class="text-center">Upload CSV for Barcodes</h1>
        <form action="{{ route('barcodes.process') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div class="mb-3">
                <label for="csv_file" class="form-label">Select CSV File</label>
                <input type="file" class="form-control" id="csv_file" name="csv_file" required>
            </div>
            <button type="submit" class="btn btn-primary">Generate Barcodes</button>
        </form>
    </div>
</x-app-layout>
