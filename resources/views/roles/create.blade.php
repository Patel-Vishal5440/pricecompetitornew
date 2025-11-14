@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
    <div class="contents">
        <div class="">
            <div class="card shadow-sm">
                <div class="card-header py-4">
                    @if (isset($role))
                        <h4>Edit Role</h4>
                    @else
                        <h4>Create Role</h4>
                    @endif
                </div>
                <div class="card-body p-4">

                    <form id="roleForm" action="{{ isset($role) ? route('roles.update', $role) : route('roles.store') }}"
                        method="POST" novalidate>
                        @csrf
                        @if (isset($role))
                            @method('PUT')
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Role Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control border @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $role->name ?? '') }}"
                                    minlength="3" maxlength="255" pattern="^[a-zA-Z0-9\s._-]+$"
                                    placeholder="Enter role name (e.g., Admin, Manager)">
                                <div class="invalid-feedback" id="name-error">
                                    @error('name')
                                        {{ $message }}
                                    @else
                                        Please enter a valid role name (3-255 characters, letters, numbers, spaces, dots,
                                        underscores, hyphens only)
                                    @enderror
                                </div>
                                <div class="form-text" id="name-help">
                                    <small class="text-muted">3-255 characters allowed</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="description" class="form-label">Description<span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control border @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3" maxlength="500" placeholder="Enter role description">{{ old('description', $role->description ?? '') }}</textarea>
                                <div class="invalid-feedback" id="description-error">
                                    @error('description')
                                        {{ $message }}
                                    @else
                                        Description cannot exceed 500 characters
                                    @enderror
                                </div>
                                <div class="form-text" id="description-help">
                                    <small class="text-muted"><span id="char-count">0</span>/500 characters</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Permissions <span class="text-danger">*</span></label>
                            <div class="row">
                                @php
                                    $permissionsByGroup = $permissions->groupBy('group');
                                    $rolePermissionIds = isset($role) ? $role->permissions->pluck('id')->toArray() : [];
                                @endphp

                                @foreach ($permissionsByGroup as $group => $groupPermissions)
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-header py-2 border">
                                                <strong>{{ $group }}</strong>
                                            </div>
                                            <div class="card-body py-2 border">
                                                @foreach ($groupPermissions as $permission)
                                                    <div class="form-check mb-1">
                                                        <input class="form-check-input permission-checkbox" type="checkbox"
                                                            name="permissions[]" value="{{ $permission->id }}"
                                                            id="permission_{{ $permission->id }}"
                                                            {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="permission_{{ $permission->id }}">
                                                            {{ $permission->description }}
                                                            <small
                                                                class="text-muted d-block">{{ $permission->name }}</small>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="invalid-feedback" id="permissions-error">
                                @error('permissions')
                                    {{ $message }}
                                @else
                                    Please select at least one permission
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center my-4">
                            <div id="validation-status" class="text-muted small">
                                <i class="fas fa-info-circle"></i> Please fill in all required fields
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('roles.index') }}" class="btn btn-light px-4 mx-1"> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4 mx-1" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true"></span>
                                    {{ isset($role) ? 'Update Role' : 'Create Role' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Role form validation script loaded');

            const form = document.getElementById('roleForm');
            const nameInput = document.getElementById('name');
            const descriptionInput = document.getElementById('description');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
            const submitBtn = document.getElementById('submitBtn');
            const validationStatus = document.getElementById('validation-status');

            // Check if all required elements exist
            if (!form || !nameInput || !descriptionInput || !submitBtn || !validationStatus) {
                console.error('Required form elements not found');
                return;
            }

            const spinner = submitBtn.querySelector('.spinner-border');

            // Character counter element
            const charCountElement = document.getElementById('char-count');

            const patterns = {
                name: /^[a-zA-Z0-9\s._-]+$/,
                description: /^[\s\S]*$/
            };

            // Update character counter
            function updateCharCount() {
                if (charCountElement) {
                    const count = descriptionInput.value.length;
                    charCountElement.textContent = count;

                    // Change color based on length
                    if (count > 450) {
                        charCountElement.style.color = '#dc3545';
                    } else if (count > 400) {
                        charCountElement.style.color = '#ffc107';
                    } else {
                        charCountElement.style.color = '#6c757d';
                    }
                }
            }

            function validateName() {
                const value = nameInput.value.trim();
                console.log('Validating name:', value);

                if (!value) {
                    showError(nameInput, 'Role name is required');
                    return false;
                }

                if (value.length < 3) {
                    showError(nameInput, 'Role name must be at least 3 characters long');
                    return false;
                }

                if (value.length > 255) {
                    showError(nameInput, 'Role name cannot exceed 255 characters');
                    return false;
                }

                if (!patterns.name.test(value)) {
                    showError(nameInput,
                        'Role name can only contain letters, numbers, spaces, dots, underscores, and hyphens');
                    return false;
                }

                showSuccess(nameInput);
                return true;
            }

            function validateDescription() {
                const value = descriptionInput.value.trim();
                console.log('Validating description:', value);

                if (!value) {
                    showError(descriptionInput, 'Description is required');
                    return false;
                }

                if (value.length < 5) {
                    showError(descriptionInput, 'Description must be at least 5 characters long');
                    return false;
                }

                if (value.length > 500) {
                    showError(descriptionInput, 'Description cannot exceed 500 characters');
                    return false;
                }

                showSuccess(descriptionInput);
                return true;
            }

            function validatePermissions() {
                const checkedPermissions = document.querySelectorAll('.permission-checkbox:checked');
                console.log('Validating permissions. Checked count:', checkedPermissions.length);

                if (checkedPermissions.length === 0) {
                    showPermissionsError('Please select at least one permission');
                    return false;
                }

                clearPermissionsError();
                return true;
            }

            function validateForm() {
                const isNameValid = validateName();
                const isDescriptionValid = validateDescription();
                const isPermissionsValid = validatePermissions();

                const isValid = isNameValid && isDescriptionValid && isPermissionsValid;

                updateValidationStatus(isValid);
                return isValid;
            }

            function updateValidationStatus(isValid = null) {
                if (isValid === null) {
                    // Auto-detect validation status
                    const isNameValid = nameInput.classList.contains('is-valid') || (!nameInput.classList.contains(
                        'is-invalid') && nameInput.value.trim());
                    const isDescriptionValid = descriptionInput.classList.contains('is-valid') || (!descriptionInput
                        .classList.contains('is-invalid') && descriptionInput.value.trim());
                    const isPermissionsValid = document.querySelectorAll('.permission-checkbox:checked').length > 0;
                    isValid = isNameValid && isDescriptionValid && isPermissionsValid;
                }

                if (isValid) {
                    validationStatus.innerHTML =
                        '<i class="fas fa-check-circle text-success"></i> All fields are valid';
                    validationStatus.className = 'text-success small';
                } else {
                    validationStatus.innerHTML =
                        '<i class="fas fa-exclamation-triangle text-warning"></i> Please fix validation errors';
                    validationStatus.className = 'text-warning small';
                }
            }

            function showError(input, message) {
                if (!input) return;

                input.classList.remove('is-valid');
                input.classList.add('is-invalid');

                const errorElement = input.parentNode.querySelector('.invalid-feedback');
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                }
            }

            function showSuccess(input) {
                if (!input) return;

                input.classList.remove('is-invalid');
                input.classList.add('is-valid');

                const errorElement = input.parentNode.querySelector('.invalid-feedback');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }

                setTimeout(() => {
                    if (input.classList.contains('is-valid')) {
                        input.classList.remove('is-valid');
                    }
                }, 2000);
            }

            function showPermissionsError(message) {
                const errorElement = document.getElementById('permissions-error');
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                }
            }

            function clearPermissionsError() {
                const errorElement = document.getElementById('permissions-error');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
            }

            // Event Listeners
            nameInput.addEventListener('input', () => {
                validateName();
                updateValidationStatus();
            });

            descriptionInput.addEventListener('input', () => {
                validateDescription();
                updateValidationStatus();
                updateCharCount(); // Update character counter on input
            });

            permissionCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    validatePermissions();
                    updateValidationStatus();
                });
            });

            nameInput.addEventListener('blur', () => {
                validateName();
                updateValidationStatus();
            });

            descriptionInput.addEventListener('blur', () => {
                validateDescription();
                updateValidationStatus();
                updateCharCount(); // Update character counter on blur
            });

            // Add paste event listeners for better UX
            nameInput.addEventListener('paste', () => {
                setTimeout(() => {
                    validateName();
                    updateValidationStatus();
                }, 100);
            });

            descriptionInput.addEventListener('paste', () => {
                setTimeout(() => {
                    validateDescription();
                    updateValidationStatus();
                    updateCharCount();
                }, 100);
            });

            [nameInput, descriptionInput].forEach(input => {
                input.addEventListener('focus', () => {
                    if (!input.classList.contains('is-invalid') || input.value.trim()) {
                        input.classList.remove('is-valid', 'is-invalid');
                    }
                });

                input.addEventListener('keyup', () => {
                    if (input.classList.contains('is-invalid')) {
                        if (input.id === 'name') validateName();
                        if (input.id === 'description') validateDescription();
                    }
                });
            });

            // Form submission handler
            form.addEventListener('submit', function(e) {
                console.log('Form submission attempted');

                if (!validateForm()) {
                    console.log('Validation failed, preventing submission');
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
                    console.log('Validation passed, allowing submission');
                    // Show loading state
                    submitBtn.disabled = true;
                    if (spinner) {
                        spinner.classList.remove('d-none');
                    }
                }
            });

            // Initial validation
            if (nameInput.value.trim()) validateName();
            if (descriptionInput.value.trim()) validateDescription();
            validatePermissions();
            updateValidationStatus();
            updateCharCount(); // Initial character count

            // Test validation function for debugging
            window.testRoleValidation = function() {
                console.log('=== Testing Role Validation ===');
                console.log('Name input:', nameInput.value);
                console.log('Description input:', descriptionInput.value);
                console.log('Permissions checked:', document.querySelectorAll('.permission-checkbox:checked')
                    .length);

                const nameValid = validateName();
                const descValid = validateDescription();
                const permValid = validatePermissions();

                console.log('Name valid:', nameValid);
                console.log('Description valid:', descValid);
                console.log('Permissions valid:', permValid);

                const formValid = validateForm();
                console.log('Form valid:', formValid);

                return formValid;
            };

            console.log('Client-side validation setup complete');
            console.log('Test validation with: testRoleValidation()');
        });
    </script>
@endpush


@push('styles')
    <style>
        .card-header.bg-light.border-bottom-0 {
            background: #8d8bbd !important;
            color: #fff !important;
            border-radius: 0.5rem 0.5rem 0 0;
            font-weight: 600;
            font-size: 1rem;
        }

        .card.shadow-sm {
            margin-bottom: 3rem !important;
        }

        .form-control.is-valid {
            border-color: #198754;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.94-.94 3.03-3.03-1.06-1.06-1.97 1.97-.94.94-1.06-1.06z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4m0-1.4-1.4 1.4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        #permissions-error {
            display: none;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-control.is-valid:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
        }

        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }

        .permission-checkbox:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .permission-checkbox:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Validation status styling */
        #validation-status {
            transition: all 0.3s ease;
        }

        #validation-status.text-success {
            font-weight: 500;
        }

        #validation-status.text-warning {
            font-weight: 500;
        }

        /* Character counter styling */
        #char-count {
            font-weight: 600;
            transition: color 0.3s ease;
        }

        /* Form text styling */
        .form-text {
            margin-top: 0.25rem;
            font-size: 0.875em;
        }

        /* Card styling for permissions */
        .card.shadow-sm .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }

        .card.shadow-sm .card-body {
            background-color: #fff;
        }

        /* Checkbox styling */
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
@endpush
