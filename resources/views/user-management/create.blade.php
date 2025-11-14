@extends('layouts.app')
@section('title', $pageTitle)
@section('content')
    <div class="contents">
        <div class="">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="card">
                        <div class="card-header py-4">
                            @if (isset($user))
                                <h4>Edit User Form</h4>
                            @else
                                <h4>New User Form</h4>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <form
                                action="{{ isset($user) ? route('user-management.update', $user) : route('user-management.store') }}"
                                method="POST" id="userForm" novalidate>
                                @csrf
                                @if (isset($user))
                                    @method('PUT')
                                @endif

                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name"
                                            value="{{ old('name', isset($user) ? $user->name : '') }}"
                                            placeholder="Enter full name" required>
                                        <div class="invalid-feedback" id="name-error"></div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email"
                                            value="{{ old('email', isset($user) ? $user->email : '') }}"
                                            placeholder="Enter email address" required>
                                        <div class="invalid-feedback" id="email-error"></div>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @if (!isset($user))
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label">Password <span
                                                    class="text-danger">{{ isset($user) ? '' : '*' }}</span></label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                name="password" placeholder="Enter password"
                                                {{ isset($user) ? '' : 'required' }}>
                                            <div class="form-text" id="password-strength"></div>
                                            <div class="invalid-feedback" id="password-error"></div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password <span
                                                    class="text-danger">{{ isset($user) ? '' : '*' }}</span></label>
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation" placeholder="Confirm password"
                                                {{ isset($user) ? '' : 'required' }}>
                                            <div class="invalid-feedback" id="password-confirmation-error"></div>
                                        </div>
                                    @endif
                                    <div class="col-md-6 mb-3">
                                        <label for="role_id" class="form-label">Role</label>
                                        <select class="form-control @error('role_id') is-invalid @enderror" id="role_id"
                                            name="role_id">
                                            <option value="">Select Role</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ old('role_id', isset($user) ? $user->role_id : '') == $role->id ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }} - {{ $role->description }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="role-error"></div>
                                        @error('role_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone_number" class="form-label">Phone Number</label>
                                        <input type="text"
                                            class="form-control @error('phone_number') is-invalid @enderror"
                                            id="phone_number" name="phone_number"
                                            value="{{ old('phone_number', isset($user) ? $user->phone_number : '') }}"
                                            placeholder="Enter phone number">
                                        <div class="invalid-feedback" id="phone-error"></div>
                                        @error('phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="company_name" class="form-label">Company Name</label>
                                        <input type="text"
                                            class="form-control @error('company_name') is-invalid @enderror"
                                            id="company_name" name="company_name"
                                            value="{{ old('company_name', isset($user) ? $user->company_name : '') }}"
                                            placeholder="Enter company name">
                                        @error('company_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="url" class="form-control @error('website') is-invalid @enderror"
                                            id="website" name="website"
                                            value="{{ old('website', isset($user) ? $user->website : '') }}"
                                            placeholder="https://example.com">
                                        <div class="invalid-feedback" id="website-error"></div>
                                        @error('website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control @error('country') is-invalid @enderror"
                                            id="country" name="country"
                                            value="{{ old('country', isset($user) ? $user->country : '') }}"
                                            placeholder="Enter country">
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                                            id="city" name="city"
                                            value="{{ old('city', isset($user) ? $user->city : '') }}"
                                            placeholder="Enter city">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3"
                                            placeholder="Enter bio information">{{ old('bio', isset($user) ? $user->bio : '') }}</textarea>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-4">
                                    <a href="{{ route('user-management.index') }}" class="btn btn-light mx-1">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4 mx-1" id="submitBtn">
                                        {{ isset($user) ? 'Update User' : 'Create User' }}
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
            console.log('Client-side validation script loaded');

            const form = document.getElementById('userForm');
            const submitBtn = document.getElementById('submitBtn');
            const isEditMode = {{ isset($user) ? 'true' : 'false' }};

            if (!form) {
                console.error('Form not found');
                return;
            }

            console.log('Edit mode:', isEditMode);

            // Form elements
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const phoneInput = document.getElementById('phone_number');
            const websiteInput = document.getElementById('website');
            const roleSelect = document.getElementById('role_id');

            // Error elements
            const nameError = document.getElementById('name-error');
            const emailError = document.getElementById('email-error');
            const passwordError = document.getElementById('password-error');
            const passwordConfirmationError = document.getElementById('password-confirmation-error');
            const phoneError = document.getElementById('phone-error');
            const websiteError = document.getElementById('website-error');
            const passwordStrength = document.getElementById('password-strength');
            const roleError = document.getElementById('role-error');

            // Check if password fields exist (for edit mode)
            const passwordFieldsExist = passwordInput && passwordConfirmationInput;
            console.log('Password fields exist:', passwordFieldsExist);

            // Validation patterns
            const patterns = {
                email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                phone: /^[\+]?[1-9][\d]{0,15}$/,
                website: /^https?:\/\/.+/,
                password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
                name: /^[a-zA-Z\s]{2,50}$/
            };

            // Validation functions
            function validateName() {
                const name = nameInput.value.trim();
                if (!name) {
                    showError(nameInput, nameError, 'Full name is required');
                    return false;
                }
                if (name.length < 2) {
                    showError(nameInput, nameError, 'Full name must be at least 2 characters');
                    return false;
                }
                if (name.length > 50) {
                    showError(nameInput, nameError, 'Full name must be less than 50 characters');
                    return false;
                }
                if (!patterns.name.test(name)) {
                    showError(nameInput, nameError, 'Full name can only contain letters and spaces');
                    return false;
                }
                hideError(nameInput, nameError);
                return true;
            }

            function validateEmail() {
                const email = emailInput.value.trim();
                if (!email) {
                    showError(emailInput, emailError, 'Email address is required');
                    return false;
                }
                if (!patterns.email.test(email)) {
                    showError(emailInput, emailError, 'Please enter a valid email address');
                    return false;
                }
                hideError(emailInput, emailError);
                return true;
            }

            function validatePassword() {
                if (!passwordFieldsExist) return true; // Skip if fields don't exist (edit mode)

                const password = passwordInput.value;

                if (!isEditMode && !password) {
                    showError(passwordInput, passwordError, 'Password is required');
                    return false;
                }

                if (password && !patterns.password.test(password)) {
                    showError(passwordInput, passwordError,
                        'Password must contain at least 8 characters, one uppercase letter, one lowercase letter, one number, and one special character'
                        );
                    return false;
                }

                if (password) {
                    let strength = 0;
                    let feedback = [];

                    if (password.length >= 8) strength++;
                    if (/[a-z]/.test(password)) strength++;
                    if (/[A-Z]/.test(password)) strength++;
                    if (/\d/.test(password)) strength++;
                    if (/[@$!%*?&]/.test(password)) strength++;

                    switch (strength) {
                        case 0:
                        case 1:
                            passwordStrength.innerHTML = '<span class="text-danger">Very Weak</span>';
                            break;
                        case 2:
                            passwordStrength.innerHTML = '<span class="text-warning">Weak</span>';
                            break;
                        case 3:
                            passwordStrength.innerHTML = '<span class="text-info">Fair</span>';
                            break;
                        case 4:
                            passwordStrength.innerHTML = '<span class="text-primary">Good</span>';
                            break;
                        case 5:
                            passwordStrength.innerHTML = '<span class="text-success">Strong</span>';
                            break;
                    }
                } else {
                    passwordStrength.innerHTML = '';
                }

                hideError(passwordInput, passwordError);
                return true;
            }

            function validatePasswordConfirmation() {
                if (!passwordFieldsExist) return true; // Skip if fields don't exist (edit mode)

                const password = passwordInput.value;
                const confirmation = passwordConfirmationInput.value;

                if (!isEditMode && !confirmation) {
                    showError(passwordConfirmationInput, passwordConfirmationError, 'Please confirm your password');
                    return false;
                }

                if (password && confirmation && password !== confirmation) {
                    showError(passwordConfirmationInput, passwordConfirmationError, 'Passwords do not match');
                    return false;
                }

                hideError(passwordConfirmationInput, passwordConfirmationError);
                return true;
            }

            function validatePhone() {
                const phone = phoneInput.value.trim();
                if (phone && !patterns.phone.test(phone)) {
                    showError(phoneInput, phoneError, 'Please enter a valid phone number');
                    return false;
                }
                hideError(phoneInput, phoneError);
                return true;
            }

            function validateWebsite() {
                const website = websiteInput.value.trim();
                if (website && !patterns.website.test(website)) {
                    showError(websiteInput, websiteError,
                        'Please enter a valid website URL (e.g., https://example.com)');
                    return false;
                }
                hideError(websiteInput, websiteError);
                return true;
            }

            function validateRole() {
                const role = roleSelect.value;
                if (!role) {
                    showError(roleSelect, roleError, 'Please select a role');
                    return false;
                }
                hideError(roleSelect, roleError);
                return true;
            }

            function showError(input, errorElement, message) {
                input.classList.add('is-invalid');
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                }
            }

            function hideError(input, errorElement) {
                input.classList.remove('is-invalid');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
            }

            function validateForm() {
                const validations = [
                    validateName(),
                    validateEmail(),
                    validatePassword(),
                    validatePasswordConfirmation(),
                    validateRole(),
                    validatePhone(),
                    validateWebsite()
                ];

                return validations.every(validation => validation);
            }

            // Event listeners
            nameInput.addEventListener('blur', validateName);
            nameInput.addEventListener('input', () => {
                if (nameInput.classList.contains('is-invalid')) {
                    validateName();
                }
            });

            emailInput.addEventListener('blur', validateEmail);
            emailInput.addEventListener('input', () => {
                if (emailInput.classList.contains('is-invalid')) {
                    validateEmail();
                }
            });

            // Only add password event listeners if fields exist
            if (passwordFieldsExist) {
                passwordInput.addEventListener('blur', validatePassword);
                passwordInput.addEventListener('input', () => {
                    validatePassword();
                    if (passwordConfirmationInput.value) {
                        validatePasswordConfirmation();
                    }
                });

                passwordConfirmationInput.addEventListener('blur', validatePasswordConfirmation);
                passwordConfirmationInput.addEventListener('input', () => {
                    if (passwordConfirmationInput.classList.contains('is-invalid')) {
                        validatePasswordConfirmation();
                    }
                });
            }

            phoneInput.addEventListener('blur', validatePhone);
            phoneInput.addEventListener('input', () => {
                if (phoneInput.classList.contains('is-invalid')) {
                    validatePhone();
                }
            });

            websiteInput.addEventListener('blur', validateWebsite);
            websiteInput.addEventListener('input', () => {
                if (websiteInput.classList.contains('is-invalid')) {
                    validateWebsite();
                }
            });

            roleSelect.addEventListener('change', validateRole);

            // Form submission
            form.addEventListener('submit', function(e) {
                console.log('Form submission attempted');
                if (!validateForm()) {
                    console.log('Validation failed, preventing submission');
                    e.preventDefault();
                    submitBtn.disabled = true;

                    // Re-enable button after 2 seconds
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
                }
            });

            // Real-time validation for better UX
            const inputs = [nameInput, emailInput, phoneInput, websiteInput, roleSelect];
            if (passwordFieldsExist) {
                inputs.push(passwordInput, passwordConfirmationInput);
            }

            inputs.forEach(input => {
                input.addEventListener('keyup', function() {
                    if (this.classList.contains('is-invalid')) {
                        switch (this.id) {
                            case 'name':
                                validateName();
                                break;
                            case 'email':
                                validateEmail();
                                break;
                            case 'password':
                                validatePassword();
                                break;
                            case 'password_confirmation':
                                validatePasswordConfirmation();
                                break;
                            case 'phone_number':
                                validatePhone();
                                break;
                            case 'website':
                                validateWebsite();
                                break;
                            case 'role_id':
                                validateRole();
                                break;
                        }
                    }
                });
            });
        });
    </script>
@endsection
