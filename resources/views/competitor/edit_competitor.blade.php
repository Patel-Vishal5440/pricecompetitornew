@extends('layouts.app')
@section('content')
    <div class="contents mt-5">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-Vertical card-default card-md mb-4">
                        <div class="card-header">
                            <h4>Edit Competitor Form</h4>
                        </div>
                        <div class="card-body pb-md-30">
                            <div class="Vertical-form">
                                @foreach ($find_competitor as $competitor)
                                    <form action="{{ route('competitor.update', $competitor->id) }}" method="POST"
                                        id="competitorEditForm">
                                        @csrf
                                        <div class="form-group">
                                            <label for="name" class="color-dark fs-14 fw-500 align-center">Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                name="name" value="{{ $competitor->name }}" id="name"
                                                placeholder="Name" required>
                                            @if ($errors->has('name'))
                                                <p class="text-danger">{{ $errors->first('name') }}</p>
                                            @endif
                                            <div class="text-danger" id="name-error"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="website"
                                                class="color-dark fs-14 fw-500 align-center">Website</label>
                                            <input type="url"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                name="website" id="website" value="{{ $competitor->website }}"
                                                placeholder="Website URL">
                                            @if ($errors->has('website'))
                                                <p class="text-danger">{{ $errors->first('website') }}</p>
                                            @endif
                                            <div class="text-danger" id="website-error"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="shortname" class="color-dark fs-14 fw-500 align-center">Short Name
                                                <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                name="shortname" value="{{ $competitor->shortname }}" id="shortname"
                                                placeholder="Short Name" required>
                                            @if ($errors->has('shortname'))
                                                <p class="text-danger">{{ $errors->first('shortname') }}</p>
                                            @endif
                                            <div class="text-danger" id="shortname-error"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="price_class_name" class="color-dark fs-14 fw-500 align-center">Price
                                                Class Name <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                                name="price_class_name" id="price_class_name"
                                                value="{{ $competitor->price_class_name }}" placeholder="Price Class Name"
                                                required>
                                            @if ($errors->has('price_class_name'))
                                                <p class="text-danger">{{ $errors->first('price_class_name') }}</p>
                                            @endif
                                            <div class="text-danger" id="price_class_name-error"></div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row justify-content-end align-items-center">
                                                <div class="layout-button mt-25">
                                                    <button type="button" onclick="window.location='/competitor/list'"
                                                        class="btn btn-danger btn-squared">Cancel</button>
                                                    <button type="submit"
                                                        class="btn btn-success btn-squared">Update</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('competitorEditForm');
            const nameInput = document.getElementById('name');
            const websiteInput = document.getElementById('website');
            const shortnameInput = document.getElementById('shortname');
            const priceClassNameInput = document.getElementById('price_class_name');
            const statusInput = document.getElementById('status');

            // Real-time validation
            nameInput.addEventListener('blur', function() {
                validateName();
            });

            websiteInput.addEventListener('blur', function() {
                validateWebsite();
            });

            shortnameInput.addEventListener('blur', function() {
                validateShortname();
            });

            priceClassNameInput.addEventListener('blur', function() {
                validatePriceClassName();
            });

            statusInput.addEventListener('change', function() {
                validateStatus();
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                let isValid = true;

                if (!validateName()) isValid = false;
                if (!validateWebsite()) isValid = false;
                if (!validateShortname()) isValid = false;
                if (!validatePriceClassName()) isValid = false;
                if (!validateStatus()) isValid = false;

                if (!isValid) {
                    e.preventDefault();
                }
            });

            function validateName() {
                const name = nameInput.value.trim();
                const errorElement = document.getElementById('name-error');

                if (name === '') {
                    errorElement.textContent = 'Name is required';
                    nameInput.classList.add('is-invalid');
                    return false;
                } else if (name.length > 255) {
                    errorElement.textContent = 'Name must not exceed 255 characters';
                    nameInput.classList.add('is-invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    nameInput.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateWebsite() {
                const website = websiteInput.value.trim();
                const errorElement = document.getElementById('website-error');
                const urlPattern = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;

                if (website !== '' && !urlPattern.test(website)) {
                    errorElement.textContent = 'Please enter a valid website URL';
                    websiteInput.classList.add('is-invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    websiteInput.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateShortname() {
                const shortname = shortnameInput.value.trim();
                const errorElement = document.getElementById('shortname-error');

                if (shortname === '') {
                    errorElement.textContent = 'Short name is required';
                    shortnameInput.classList.add('is-invalid');
                    return false;
                } else if (shortname.length > 100) {
                    errorElement.textContent = 'Short name must not exceed 100 characters';
                    shortnameInput.classList.add('is-invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    shortnameInput.classList.remove('is-invalid');
                    return true;
                }
            }

            function validatePriceClassName() {
                const priceClassName = priceClassNameInput.value.trim();
                const errorElement = document.getElementById('price_class_name-error');

                if (priceClassName === '') {
                    errorElement.textContent = 'Price class name is required';
                    priceClassNameInput.classList.add('is-invalid');
                    return false;
                } else if (priceClassName.length > 255) {
                    errorElement.textContent = 'Price class name must not exceed 255 characters';
                    priceClassNameInput.classList.add('is-invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    priceClassNameInput.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateStatus() {
                const status = statusInput.value;
                const errorElement = document.getElementById('status-error');

                if (status === '') {
                    errorElement.textContent = 'Status is required';
                    statusInput.classList.add('is-invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    statusInput.classList.remove('is-invalid');
                    return true;
                }
            }
        });
    </script>
@endsection
