<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <div class="row mb-4">
        <div class="col-sm-6 col-lg-6">
            {{-- <h4 class="mb-2">User Details</h4> --}}
        </div>
        <div class="col-sm-6 col-lg-6 d-flex justify-content-end">
            {{-- <button type="button" class="btn btn-primary waves-effect waves-light me-3" data-bs-toggle="modal"
                data-bs-target="#newUserDetails">Import User Details.</button>

            <a href="{{ route('label-detail.download.csv') }}" download=""
                class="btn btn-secondary waves-effect waves-light  me-3">Sample CSV</a> --}}

            <a href="{{ route('users.create') }}" class="btn btn-info waves-effect waves-light">
                Create
            </a>
        </div>
    </div>
    @include('message')
    <!-- Content -->
    <div class="row">
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="card">
                <h5 class="card-header">All Users</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Per Row Amount</th>
                                <th>Wallet Amount</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">

                            @foreach ($users as $index => $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->per_row_amount }}</td>
                                    <td>{{ $user->wallet_amount }}</td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            {{ $role->name }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="btn btn-primary waves-effect waves-light">
                                                Edit
                                            </a>
                                            |
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger waves-effect waves-light">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <!-- / Content -->


</x-app-layout>
