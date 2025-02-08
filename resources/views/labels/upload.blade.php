<x-app-layout>
    <div class="container mt-5">
        <h1 class="text-center">Upload CSV to Generate Labels</h1>
        <form action="{{ route('labels.process') }}" method="POST" enctype="multipart/form-data" class="mt-4">
            @csrf
            <div class="mb-3">
                <label for="csv_file" class="form-label">Choose CSV File</label>
                <input type="file" name="csv_file" id="csv_file" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Generate and Download ZIP</button>
        </form>
    </div>
</x-app-layout>
