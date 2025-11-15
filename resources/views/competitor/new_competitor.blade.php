@extends('layouts.app')
@section('content')
    <div class="contents">
        <div class="">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-Vertical card-default card-md">
                        <div class="card-header py-4">
                            <h4>{{ isset($competitor) ? 'Edit Competitor Form' : 'New Competitor Form' }}</h4>
                        </div>
                        <div class="card-body pb-md-30">
                            <div class="Vertical-form">
                                <form action="{{ isset($competitor) ? route('competitor.update', $competitor->id) : route('competitor.store') }}" method="POST" id="competitorForm">
                                    @csrf
                                    @if(isset($competitor))
                                        @method('PUT')
                                    @endif
                                    
                                    <div class="form-group">
                                        <label for="name" class="color-dark fs-14 fw-500 align-center">Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                            name="name" value="{{ old('name', isset($competitor) ? $competitor->name : '') }}" id="name" placeholder="Name">
                                        @if ($errors->has('name'))
                                            <p class="text-danger">{{ $errors->first('name') }}</p>
                                        @endif
                                        <div class="text-danger" id="name-error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="website" class="color-dark fs-14 fw-500 align-center">Website<span
                                                class="text-danger">*</span></label>
                                        <input type="url" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                            name="website" id="website" value="{{ old('website', isset($competitor) ? $competitor->website : '') }}"
                                            placeholder="Website URL">
                                        @if ($errors->has('website'))
                                            <p class="text-danger">{{ $errors->first('website') }}</p>
                                        @endif
                                        <div class="text-danger" id="website-error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="shortname" class="color-dark fs-14 fw-500 align-center">Short Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                            name="shortname" value="{{ old('shortname', isset($competitor) ? $competitor->shortname : '') }}" id="shortname"
                                            placeholder="Short Name">
                                        @if ($errors->has('shortname'))
                                            <p class="text-danger">{{ $errors->first('shortname') }}</p>
                                        @endif
                                        <div class="text-danger" id="shortname-error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="price_class_name" class="color-dark fs-14 fw-500 align-center">Price
                                            Class Name <span class="text-danger"></span></label>
                                        <input type="text" class="form-control ih-medium ip-gray radius-xs b-light px-15"
                                            name="price_class_name" id="price_class_name"
                                            value="{{ old('price_class_name', isset($competitor) ? $competitor->price_class_name : '') }}" placeholder="Price Class Name">
                                        @if ($errors->has('price_class_name'))
                                            <p class="text-danger">{{ $errors->first('price_class_name') }}</p>
                                        @endif
                                        <div class="text-danger" id="price_class_name-error"></div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="button" onclick="window.location='/competitor/list'"
                                            class="btn btn-light px-4 mx-1">Cancel</button>
                                        <button type="submit" class="btn btn-primary px-4 mx-1">{{ isset($competitor) ? 'Update' : 'Save' }}</button>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('competitorForm');
            const nameInput = document.getElementById('name');
            const websiteInput = document.getElementById('website');
            const shortnameInput = document.getElementById('shortname');
            const priceClassNameInput = document.getElementById('price_class_name');
            const isEditMode = {{ isset($competitor) ? 'true' : 'false' }};

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

            // Form submission with AJAX
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                let isValid = true;

                if (!validateName()) isValid = false;
                if (!validateWebsite()) isValid = false;
                if (!validateShortname()) isValid = false;
                if (!validatePriceClassName()) isValid = false;

                if (!isValid) {
                    return;
                }

                // Clear all previous errors
                document.querySelectorAll('.text-danger[id$="-error"]').forEach(el => {
                    el.textContent = '';
                });
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });

                // Get form data
                const formData = new FormData(form);
                const submitButton = form.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.textContent;
                
                // Disable submit button
                submitButton.disabled = true;
                submitButton.textContent = isEditMode ? 'Updating...' : 'Saving...';

                // Determine URL
                const url = form.getAttribute('action');
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 document.querySelector('input[name="_token"]')?.value;

                // Submit via AJAX using fetch
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => {
                    // Check if response is ok
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;

                    if (data.success) {
                        // Redirect to list page immediately
                        window.location.href = '/competitor/list';
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                const errorElement = document.getElementById(field + '-error');
                                const inputElement = document.getElementById(field);
                                if (errorElement && inputElement) {
                                    errorElement.textContent = Array.isArray(data.errors[field]) 
                                        ? data.errors[field][0] 
                                        : data.errors[field];
                                    inputElement.classList.add('is-invalid');
                                }
                            });
                        }
                        
                        // Show error message
                        if (typeof toastr !== 'undefined') {
                            toastr.error(data.message || 'Validation failed');
                        } else {
                            alert(data.message || 'Validation failed');
                        }
                    }
                })
                .catch(error => {
                    submitButton.disabled = false;
                    submitButton.textContent = originalButtonText;
                    
                    console.error('Error:', error);
                    
                    // Handle validation errors from catch
                    if (error.errors) {
                        Object.keys(error.errors).forEach(field => {
                            const errorElement = document.getElementById(field + '-error');
                            const inputElement = document.getElementById(field);
                            if (errorElement && inputElement) {
                                errorElement.textContent = Array.isArray(error.errors[field]) 
                                    ? error.errors[field][0] 
                                    : error.errors[field];
                                inputElement.classList.add('is-invalid');
                            }
                        });
                    }
                    
                    const errorMessage = error.message || 'An error occurred. Please try again.';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                });
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

                if (website === '') {
                    errorElement.textContent = 'Website is required';
                    websiteInput.classList.add('is-invalid');
                    return false;
                } else if (!urlPattern.test(website)) {
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

                if (priceClassName.length > 255) {
                    errorElement.textContent = 'Price class name must not exceed 255 characters';
                    priceClassNameInput.classList.add('is-invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    priceClassNameInput.classList.remove('is-invalid');
                    return true;
                }
            }
        });
    </script>
@endsection
