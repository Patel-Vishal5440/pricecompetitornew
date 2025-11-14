@extends('layouts.app')
@section('content')
    {{-- <div class="signUP-admin"> --}}
    <div class="">
        <div class="container-fluid">
            <div class="row justify-content-center">
                {{-- <div class="col-xl-4 col-lg-5 col-md-5 p-0">
                    <div class="signUP-admin-left signIn-admin-left position-relative">
                        <div class="signUP-overlay">
                            <img class="svg signupTop" src="{{ asset('img/svg/signupTop.svg') }}" alt="img"/>
                            <img class="svg signupBottom" src="{{ asset('img/svg/signupBottom.svg') }}" alt="img"/>
                        </div><!-- End: .signUP-overlay  -->
                        <div class="signUP-admin-left__content">
                            <div
                                class="text-capitalize mb-md-30 mb-15 d-flex align-items-center justify-content-md-start justify-content-center">
                                <img class="svg dark" src="{{ asset('img/Logo_Dark.png') }}" alt="">
                            </div>
                            <h1>Bootstrap 4 Laravel Web Application</h1>
                        </div><!-- End: .signUP-admin-left__content  -->
                        <div class="signUP-admin-left__img">
                            <img class="img-fluid svg" src="{{ asset('img/svg/signupIllustration.svg') }}" alt="img"/>
                        </div><!-- End: .signUP-admin-left__img  -->
                    </div><!-- End: .signUP-admin-left  -->
                </div><!-- End: .col-xl-4  --> --}}
                <div class="col-xl-8 col-lg-7 col-md-7 col-sm-8">
                    <div class="signUp-admin-right signIn-admin-right  p-md-40 p-10">
                        <div class="row justify-content-center">
                            <div class="col-xl-7 col-lg-8 col-md-12">
                                <div class="edit-profile mt-md-25 mt-0">
                                    <div class="card border-0 p-3">
                                        <div class="card-header border-0  pb-md-15 pb-10 pt-md-20 pt-10 ">
                                            <div class="edit-profile__title">
                                                <h6>Sign in</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="edit-profile__body">
                                                <form method="POST" action="{{ route('login') }}">
                                                    @csrf
                                                    <div class="form-group mb-20">
                                                        <label for="username">Email Address</label>
                                                        <input type="email"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            id="email" placeholder="Email" name="email"
                                                            value="admin@gmail.com" required>
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
                                                                name="password">
                                                            <div id="eye_icon"
                                                                class="fa fa-fw fa-eye text-light fs-16 field-icon toggle-password2">
                                                            </div>
                                                            @error('password')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="signUp-condition signIn-condition">
                                                        <div class="checkbox-theme-default custom-checkbox ">
                                                            <input class="checkbox" type="checkbox" id="check-1"
                                                                {{ old('check-1') ? 'checked' : '' }}>
                                                            <label for="check-1">
                                                                <span class="checkbox-text">Keep me logged in</span>
                                                            </label>
                                                        </div>
                                                        {{-- <a href="{{ route('password.request') }}">forget password</a> --}}
                                                    </div>
                                                    <div class="button-group d-flex pt-1 justify-content-center">
                                                        <button
                                                            class="btn btn-primary btn-default btn-squared mr-15 text-capitalize lh-normal px-50 py-15 signIn-createBtn w-100">
                                                            sign in
                                                        </button>
                                                    </div>
                                                </form>
                                                {{-- <p class="social-connector text-center mb-sm-25 mb-15  mt-sm-30 mt-20">
                                                    <span>OR</span>
                                                </p> --}}
                                                {{-- <p class="d-flex mt-3 justify-content-center">
                                                    Don't have an account?
                                                    <a href="{{ route('register') }}" class="color-primary mx-2">
                                                        Sign up
                                                    </a>
                                                </p> --}}
                                            </div>
                                        </div><!-- End: .card-body -->
                                    </div><!-- End: .card -->
                                </div><!-- End: .edit-profile -->
                            </div><!-- End: .col-xl-5 -->
                        </div>
                    </div><!-- End: .signUp-admin-right  -->
                </div><!-- End: .col-xl-8  -->
            </div>
        </div>
    </div><!-- End: .signUP-admin  -->
@endsection

@section('scripts')
    <script>
        $('#eye_icon').on('click', function() {
            if ($('#password').attr('type') == 'text') {
                $('#password').attr('type', 'password')
                $('#eye_icon').attr('class', 'fa fa-fw fa-eye text-light fs-16 field-icon toggle-password2')

            } else {
                $('#password').attr('type', 'text')
                $('#eye_icon').attr('class', 'fa fa-fw fa-eye-slash text-light fs-16 field-icon toggle-password2')
            }
        });
    </script>
@endsection
