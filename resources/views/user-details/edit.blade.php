<x-app-layout>
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-6">
            <h4 class="mb-2">
                Edit User
            </h4>
        </div>
        <div class="col-sm-6 col-lg-6 d-flex justify-content-end">
            <a href="{{ route('users.index') }}" class="btn btn-secondary waves-effect waves-light">Back</a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between px-3" role="alert">
            <div><strong>Error!</strong> {{ session('error') }}</div>
            <button type="button" class="close float-right custom-dismiss-btn" data-bs-dismiss="alert"
                aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Content -->
    <div class="col-md-12">
        <div class="card mb-4">
            <form method="POST" action="{{ route('users.update', $user->id) }}">
                @csrf
                @method('PUT') <!-- Add this for a PUT request -->

                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name"
                            value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" id="email"
                            value="{{ old('email', $user->email) }}" placeholder="name@example.com" required
                            autocomplete="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" id="password"
                            placeholder="Leave blank to keep current password" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Per Row Amount</label>
                        <input type="number" value="{{ old('per_row_amount', $user->per_row_amount) }}"
                            class="form-control" name="per_row_amount" id="" required
                            autocomplete="wallet-amount" />
                        <x-input-error :messages="$errors->get('per_row_amount')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Wallet Amount</label>
                        <input type="number" class="form-control"
                            value="{{ old('wallet_amount', $user->wallet_amount) }}" name="wallet_amount" id=""
                            required autocomplete="wallet-amount" />
                        <x-input-error :messages="$errors->get('wallet_amount')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Select a Role</label>
                        <select class="form-select" name="role_id" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role['id'] }}"
                                    {{ $user->roles->contains('id', $role['id']) ? 'selected' : '' }}>
                                    {{ $role['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- / Content -->
</x-app-layout>
