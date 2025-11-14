@extends('layouts.app')

@section('content')
<div class="contents">
    <div class="row justify-content-center my-4">
        <div class="col-lg-7 col-md-8">
            @if(session('status') === 'profile-updated')
                <div class="alert alert-success">Profile updated successfully!</div>
            @endif

            <div class="card shadow rounded">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <label for="file-upload" class="d-block cursor-pointer">
                            <img id="profile-image-preview"
                                 src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('img/author/profile.png') }}"
                                 class="rounded-circle"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                            <div class="text-muted mt-2">Click to change</div>
                        </label>
                        <input type="file" id="file-upload" name="profile_image" class="d-none" accept="image/*">
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" value="{{ $user->email }}" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                   class="form-control @error('phone_number') is-invalid @enderror">
                            @error('phone_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}"
                                   class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" value="{{ old('country', $user->country) }}"
                                   class="form-control">
                        </div>

                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" value="{{ old('city', $user->city) }}"
                                   class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Website</label>
                            <input type="url" name="website" value="{{ old('website', $user->website) }}"
                                   class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" rows="4"
                                      class="form-control">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <div class="form-group d-flex justify-content-end my-4">
                            <a href="{{ route('products.list') }}" class="btn btn-light px-4 mx-1">Cancel</a>
                            <button class="btn btn-primary px-4 mx-1">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Image upload script --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const fileInput = document.getElementById("file-upload");
    const previewImage = document.getElementById("profile-image-preview");

    fileInput.addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            // Preview
            const reader = new FileReader();
            reader.onload = function (e) {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Upload
            const formData = new FormData();
            formData.append("profile_image", file);
            formData.append("_token", "{{ csrf_token() }}");

            fetch("{{ route('profile.image.update') }}", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Image updated:", data.image_url);
                } else {
                    alert(data.message || "Image upload failed");
                }
            })
            .catch(error => {
                console.error("Upload error:", error);
                alert("An error occurred while uploading.");
            });
        }
    });
});
</script>
@endsection
