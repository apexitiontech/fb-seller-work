<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!--  Login -->
        <div class="card">
            <div class="card-body">
                <!-- Logo -->
                <div class="app-brand justify-content-center ">
                    <img src="{{ asset('assets/img/main-loog.jpg') }}" style="height:100px;"  alt="">

                </div>
                <!-- /Logo -->
                <p class="mb-4">Please sign-in to your account and start the adventure</p>

                <form id="formAuthentication" class="mb-3" action="index.html" method="GET">
                    <div class="mb-3">
                        <x-input-label for="email" :value="__('Email')" />
                        <input type="text" class="form-control" id="email" type="email" name="email"
                            :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />

                    </div>
                    <div class="mb-3 form-password-toggle">
                        <div class="d-flex justify-content-between">
                            <x-input-label for="password" :value="__('Password')" />
                            {{-- <small>Forgot Password?</small> --}}
                            </a>
                        </div>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" class="form-control" name="password"
                                class="block mt-1 w-full" required autocomplete="current-password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password" />
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            <a href="auth-forgot-password-basic.html">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember_me" name="remember" />
                            <label class="form-check-label" for="remember_me"> Remember Me </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                    </div>
                </form>

                {{-- <p class="text-center">
                    <span>New on our platform?</span>
                    <a href="auth-register-basic.html">
                        <span>Create an account</span>
                    </a>
                </p> --}}

                {{-- <div class="divider my-4">
                    <div class="divider-text">or</div>
                </div>

                <div class="d-flex justify-content-center">
                    <a href="javascript:;" class="btn btn-icon btn-label-facebook me-3">
                        <i class="tf-icons bx bxl-facebook"></i>
                    </a>

                    <a href="javascript:;" class="btn btn-icon btn-label-google-plus me-3">
                        <i class="tf-icons bx bxl-google-plus"></i>
                    </a>

                    <a href="javascript:;" class="btn btn-icon btn-label-twitter">
                        <i class="tf-icons bx bxl-twitter"></i>
                    </a>
                </div> --}}
            </div>
        </div>
        <!-- / Login -->
    </form>
</x-guest-layout>
