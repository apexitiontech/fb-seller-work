@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex justify-content-between px-3" role="alert">
        <div><strong>Success !</strong> {{ session('success') }}</div>
        <button type="button" class="close float-right custom-dismiss-btn" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between px-3" role="alert">
        <div><strong>Error !</strong> {{ session('error') }}</div>
        <button type="button" class="close float-right custom-dismiss-btn" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-info alert-dismissible fade show d-flex justify-content-between px-3" role="alert">
        <div><strong>Warning !</strong> {{ session('warning') }}</div>
        <button type="button" class="close float-right custom-dismiss-btn" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
