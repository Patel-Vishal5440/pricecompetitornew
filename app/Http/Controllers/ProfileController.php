<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function profileSetting(Request $request)
    {
        $pageTitle = 'Profile List';
        $pageDescription = 'Some description for the page';

        return view('pages.profile-setting', [
            'user' => $request->user(),
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'max:255'],
                'country' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:255'],
                'company_name' => ['nullable', 'string', 'max:255'],
                'website' => ['nullable', 'url', 'max:255'],
                'bio' => ['nullable', 'string'],
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);

            $user->fill($validated);

            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $path = $file->store('profile_images', 'public');

                if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }

                $user->profile_image = $path;
            }

            $user->save();

            return Redirect::route('products.list')->with('status', 'profile-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return Redirect::back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return Redirect::back()
                ->with('error', 'An error occurred while updating profile.')
                ->withInput();
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password updated successfully!']);
    }

    /**
     * Update the user's profile image via AJAX.
     */
    public function updateImage(Request $request)
    {
        try {
            $user = $request->user();
            $request->validate([
                'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $path = $file->store('profile_images', 'public');
                // Delete old image if exists
                if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $user->profile_image = $path;
                $user->save();
                return response()->json(['success' => true, 'image_url' => route('profile.image.show', ['v' => now()->timestamp])]);
            }
            return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to update profile image right now.'
            ], 500);
        }
    }

    /**
     * Serve logged-in user's profile image with default fallback.
     */
    public function showImage(Request $request): BinaryFileResponse
    {
        $user = $request->user();
        $defaultAvatar = public_path('img/author/profile.png');

        if (!empty($user->profile_image) && Storage::disk('public')->exists($user->profile_image)) {
            $path = storage_path('app/public/' . $user->profile_image);
            $mime = mime_content_type($path) ?: 'application/octet-stream';
            return response()->file($path, ['Content-Type' => $mime]);
        }

        return response()->file($defaultAvatar, ['Content-Type' => 'image/png']);
    }
}
