@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <div class="contents">
        <div class="">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-9 col-12">
                    <div class="card">
                        <div class="card-header py-4">
                            @if (isset($permission))
                                <h4>Edit Permission Form</h4>
                            @else
                                <h4>Create Permission Form</h4>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <form
                                action="{{ isset($permission) ? route('permissions.update', $permission) : route('permissions.store') }}"
                                method="POST" id="permissionForm">
                                @csrf
                                @if (isset($permission))
                                    @method('PUT')
                                @endif

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $permission->name ?? '') }}"
                                        placeholder="Enter permission name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="name-error"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="description" id="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        value="{{ old('description', $permission->description ?? '') }}"
                                        placeholder="Enter permission description">
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="description-error"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="group" class="form-label">Module<span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="group" id="group"
                                        class="form-control @error('group') is-invalid @enderror"
                                        value="{{ old('group', $permission->group ?? '') }}"
                                        placeholder="Enter module name">
                                    @error('group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="text-danger" id="group-error"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label><br>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            value="1"
                                            {{ old('is_active', $permission->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('permissions.index') }}" class="btn btn-light px-4 mx-1">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-4 mx-1" id="submitBtn">
                                        {{ isset($permission) ? 'Update Permission' : 'Create Permission' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('permissionForm');
            const nameInput = document.getElementById('name');
            const descriptionInput = document.getElementById('description');
            const groupInput = document.getElementById('group');
            const isActiveInput = document.getElementById('is_active');
            const submitBtn = document.getElementById('submitBtn');
            const isEditMode = {{ isset($permission) ? 'true' : 'false' }};

            // Check if all required elements exist
            if (!form || !nameInput || !descriptionInput || !groupInput || !submitBtn) {
                console.error('Required form elements not found');
                return;
            }

            // Real-time validation
            nameInput.addEventListener('blur', function() {
                validateName();
            });

            descriptionInput.addEventListener('blur', function() {
                validateDescription();
            });

            groupInput.addEventListener('blur', function() {
                validateGroup();
            });

            // Input event listeners for real-time feedback
            nameInput.addEventListener('input', function() {
                if (nameInput.classList.contains('is-invalid')) {
                    validateName();
                }
            });

            descriptionInput.addEventListener('input', function() {
                if (descriptionInput.classList.contains('is-invalid')) {
                    validateDescription();
                }
            });

            groupInput.addEventListener('input', function() {
                if (groupInput.classList.contains('is-invalid')) {
                    validateGroup();
                }
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                let isValid = true;

                if (!validateName()) isValid = false;
                if (!validateDescription()) isValid = false;
                if (!validateGroup()) isValid = false;

                if (!isValid) {
                    e.preventDefault();
                    // Disable submit button temporarily to prevent spam
                    submitBtn.disabled = true;
                    setTimeout(() => {
                        submitBtn.disabled = false;
                    }, 2000);

                    // Scroll to first error
                    const firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                } else {
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
                        (isEditMode ? 'Updating...' : 'Creating...');
                }
            });

            function validateName() {
                const name = nameInput.value.trim();
                const errorElement = document.getElementById('name-error');

                if (name === '') {
                    errorElement.textContent = 'Permission name is required';
                    nameInput.classList.add('is-invalid');
                    return false;
                } else if (name.length < 3) {
                    errorElement.textContent = 'Permission name must be at least 3 characters long';
                    nameInput.classList.add('is-invalid');
                    return false;
                } else if (name.length > 255) {
                    errorElement.textContent = 'Permission name must not exceed 255 characters';
                    nameInput.classList.add('is-invalid');
                    return false;
                } else if (!/^[a-zA-Z0-9\s._-]+$/.test(name)) {
                    errorElement.textContent =
                        'Permission name can only contain letters, numbers, spaces, dots, underscores, and hyphens';
                    nameInput.classList.add('is-invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    nameInput.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateDescription() {
                const description = descriptionInput.value.trim();
                const errorElement = document.getElementById('description-error');

                if (description === '') {
                    errorElement.textContent = 'Description is required';
                    descriptionInput.classList.add('is-invalid');
                    return false;
                } else if (description.length < 5) {
                    errorElement.textContent = 'Description must be at least 5 characters long';
                    descriptionInput.classList.add('is-invalid');
                    return false;
                } else if (description.length > 500) {
                    errorElement.textContent = 'Description must not exceed 500 characters';
                    descriptionInput.classList.add('is-invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    descriptionInput.classList.remove('is-invalid');
                    return true;
                }
            }

            function validateGroup() {
                const group = groupInput.value.trim();
                const errorElement = document.getElementById('group-error');

                if (group === '') {
                    errorElement.textContent = 'Module is required';
                    groupInput.classList.add('is-invalid');
                    return false;
                } else if (group.length < 2) {
                    errorElement.textContent = 'Module name must be at least 2 characters long';
                    groupInput.classList.add('is-invalid');
                    return false;
                } else if (group.length > 100) {
                    errorElement.textContent = 'Module name must not exceed 100 characters';
                    groupInput.classList.add('is-invalid');
                    return false;
                } else if (!/^[a-zA-Z0-9\s._-]+$/.test(group)) {
                    errorElement.textContent =
                        'Module name can only contain letters, numbers, spaces, dots, underscores, and hyphens';
                    groupInput.classList.add('is-invalid');
                    return false;
                } else {
                    errorElement.textContent = '';
                    groupInput.classList.remove('is-invalid');
                    return true;
                }
            }

            // Initial validation for edit mode
            if (isEditMode) {
                validateName();
                validateDescription();
                validateGroup();
            }

            // Test validation function for debugging
            window.testPermissionValidation = function() {
                console.log('=== Testing Permission Validation ===');
                console.log('Name input:', nameInput.value);
                console.log('Description input:', descriptionInput.value);
                console.log('Group input:', groupInput.value);

                const nameValid = validateName();
                const descValid = validateDescription();
                const groupValid = validateGroup();

                console.log('Name valid:', nameValid);
                console.log('Description valid:', descValid);
                console.log('Group valid:', groupValid);

                const formValid = nameValid && descValid && groupValid;
                console.log('Form valid:', formValid);

                return formValid;
            };

            console.log('Permission form validation setup complete');
            console.log('Test validation with: testPermissionValidation()');
        });
    </script>

    <style>
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }

        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        .text-danger {
            font-size: 0.875em;
            margin-top: 0.25rem;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-input:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endsection
