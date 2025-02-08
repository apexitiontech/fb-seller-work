<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot> --}}

    <div class="row mb-4">
        <div class="col-sm-6 col-lg-6">
            <h4 class="mb-2">
                Create a New User
            </h4>
        </div>
        <div class="col-sm-6 col-lg-6 d-flex justify-content-end">
            {{-- <button type="button" class="btn btn-primary waves-effect waves-light me-3" data-bs-toggle="modal"
                data-bs-target="#newUserDetails">Import User Details.</button> --}}

            <a href="{{ route('users.index') }}" class="btn btn-secondary waves-effect waves-light">Back</a>
        </div>
    </div>
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between px-3" role="alert">
            <div><strong>Error !</strong> {{ session('error') }}</div>
            <button type="button" class="close float-right custom-dismiss-btn" data-bs-dismiss="alert"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <!-- Content -->
    <!-- Form controls -->
    <div class="col-md-12">
        <div class="card mb-4">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <h5 class="card-header">

                </h5>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="" class="form-label">Select a Role</label>
                        <select class="form-select" name="role_id" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role['id'] }}" {{ $role['id'] == 3 ? 'selected' : '' }}>
                                    {{ $role['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name"
                            :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" id="" :value="old('email')"
                            placeholder="name@example.com" required autocomplete="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id=""
                            placeholder="Password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Wallet Amount</label>
                        <input type="number" class="form-control" name="wallet_amount" id="" required
                            autocomplete="wallet-amount" />
                        <x-input-error :messages="$errors->get('wallet_amount')" class="mt-2" />
                    </div>

                    {{-- <div class="mb-3">
                        <label for="" class="form-label">Password Confirmation</label>
                        <input type="password" class="form-control" name="password_confirmation" id=""
                            placeholder="Password Confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div> --}}
                    <div class="mb-3">
                        <label for="" class="form-label">Select a Role</label>

                        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                    </div>


                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>



                </div>
            </form>
        </div>
    </div>
    <!-- / Content -->


</x-app-layout>
