@extends('layouts.app')
@section('content')
    {{-- <div class="signUP-admin"> --}}
    <div class="">
        <div class="container-fluid">
            <div class="row justify-content-center">
                {{-- <div class="col-xl-4 col-lg-5 col-md-5 p-0">
                    <div class="signUP-admin-left position-relative">
                        <div class="signUP-overlay">
                            <img class="svg signupTop" src="{{ asset('img/svg/signupTop.svg') }}" alt="img"/>
                            <img class="svg signupBottom" src="{{ asset('img/svg/signupBottom.svg') }}" alt="img"/>
                        </div>
                        <div class="signUP-admin-left__content">
                            <div
                                class="text-capitalize mb-md-30 mb-15 d-flex align-items-center justify-content-md-start justify-content-center">
                                <img class="svg dark" src="{{ asset('img/Logo_Dark.png') }}" alt="">
                            </div>
                            <h1>Bootstrap 4 Laravel Web Application</h1>
                        </div>
                        <div class="signUP-admin-left__img">
                            <img class="img-fluid svg" src="{{ asset('img/svg/signupIllustration.svg') }}" alt="img"/>
                        </div>
                    </div>
                </div> --}}
                <div class="col-xl-8 col-lg-7 col-md-7 col-sm-8">
                    <div class="signUp-admin-right  p-md-40 p-10">
                        <div class="row justify-content-center">
                            <div class="col-xl-7 col-lg-10 col-md-12 ">
                                <div class="edit-profile mt-md-25 mt-0">
                                    <div class="card border-0 p-3">
                                        <div class="card-header border-0  pb-md-15 pb-10 pt-md-20 pt-10 ">
                                            <div class="edit-profile__title">
                                                <h6>Sign up to <span
                                                        class="color-primary">{{ env('APP_NAME', 'Admin') }}</span></h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="edit-profile__body">
                                                <form method="POST" action="{{ route('register') }}">
                                                    @csrf
                                                    <div class="form-group mb-20">
                                                        <label for="name">name</label>
                                                        <input type="text"
                                                            class="form-control @error('name') is-invalid @enderror"
                                                            id="name" name="name" value="{{ old('name') }}"
                                                            required autocomplete="name" placeholder="Full Name">
                                                        @error('name')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group mb-20">
                                                        <label for="username">username</label>
                                                        <input type="text"
                                                            class="form-control @error('username') is-invalid @enderror"
                                                            id="username" name="username" value="{{ old('username') }}"
                                                            required autocomplete="username" placeholder="Username">
                                                        @error('username')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group mb-20">
                                                        <label for="email">Email Adress</label>
                                                        <input type="text"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            name="email" value="{{ old('email') }}" required
                                                            autocomplete="email" id="email"
                                                            placeholder="name@example.com">
                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                    <div class="form-group mb-15">
                                                        <label for="password-field">password</label>
                                                        <div class="position-relative">
                                                            <input id="password" type="password"
                                                                class="form-control @error('password') is-invalid @enderror"
                                                                required autocomplete="new-password" name="password">
                                                            <span id="password_eye_icon"
                                                                class="fa fa-fw fa-eye text-light fs-16 field-icon toggle-password2"></span>
                                                            @error('password')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-15">
                                                        <label for="password-field">confirm Password</label>
                                                        <div class="position-relative">
                                                            <input id="password-confirm" type="password"
                                                                class="form-control" required autocomplete="new-password"
                                                                name="password_confirmation" required
                                                                autocomplete="new-password">
                                                            <span id="confirm_eye_icon"
                                                                class="fa fa-fw fa-eye text-light fs-16 field-icon toggle-password2"></span>
                                                        </div>
                                                    </div>
                                                    <div class="button-group d-flex pt-1 justify-content-center">
                                                        <button
                                                            class="btn btn-primary btn-default btn-squared text-capitalize lh-normal px-50 py-15 signUp-createBtn signIn-createBtn w-100">
                                                            Create Account
                                                        </button>
                                                    </div>
                                                </form>
                                                <p class="social-connector text-center mb-md-25 mb-15  mt-md-30 mt-20 ">
                                                    <span>OR</span>
                                                </p>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <p class="mb-0">
                                                        Already have an account?
                                                        <a href="{{ route('login') }}" class="color-primary">
                                                            Sign in
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#password_eye_icon').on('click', function() {
            if ($('#password').attr('type') == 'text') {
                $('#password').attr('type', 'password')
                $('#password_eye_icon').attr('class',
                    'fa fa-fw fa-eye text-light fs-16 field-icon toggle-password2')

            } else {
                $('#password').attr('type', 'text')
                $('#password_eye_icon').attr('class',
                    'fa fa-fw fa-eye-slash text-light fs-16 field-icon toggle-password2')
            }
        });
        $('#confirm_eye_icon').on('click', function() {
            if ($('#password-confirm').attr('type') == 'text') {
                $('#password-confirm').attr('type', 'password')
                $('#confirm_eye_icon').attr('class',
                    'fa fa-fw fa-eye text-light fs-16 field-icon toggle-password2')

            } else {
                $('#password-confirm').attr('type', 'text')
                $('#confirm_eye_icon').attr('class',
                    'fa fa-fw fa-eye-slash text-light fs-16 field-icon toggle-password2')
            }
        });
    </script>
@endsection
