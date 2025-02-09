<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Content -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Wallet Amount</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ number_format(auth()->user()->wallet_amount, 2) }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-user-voice bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Per Row Amount</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ number_format(auth()->user()->per_row_amount, 2) }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-user-voice bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->name == 'Super Admin' || auth()->user()->role === 'Admin')
        <div class="row mt-3">
            <div class="card">
                <h5 class="card-header">
                    Vendor Serial Numbers
                </h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Vendor Name</th>
                                <th>Used Serial Numbers</th>
                                <th>Unused Serial Numbers</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($vendors as $key => $vendor)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $vendor->name }}</td>
                                    <td>{{ $vendor->serials->where('is_link', 1)->count() }}</td>
                                    <td>{{ $vendor->serials->where('is_link', 0)->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
