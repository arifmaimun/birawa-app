<?php

namespace App\Http\Controllers;

use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->doctorProfile;

        if (! $profile) {
            $profile = DoctorProfile::create(['user_id' => $user->id]);
        }

        return view('profile.edit', compact('user', 'profile'));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            // Generate filename
            $filename = $user->id.'-'.time().'.webp';
            $path = 'avatars/'.$filename;

            try {
                // Store the file directly (it's already processed by client)
                $file->storeAs('avatars', $filename, 'public');

                // Delete old avatar
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $user->avatar = $path;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Avatar updated successfully',
                    'url' => Storage::url($path),
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Avatar upload failed: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan avatar. Silakan coba lagi.',
                ], 500);
            }
        }

        return response()->json(['message' => 'Tidak ada file yang diupload'], 400);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $profile = $user->doctorProfile;

        if (! $profile) {
            $profile = DoctorProfile::create(['user_id' => $user->id]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|max:10240', // 10MB to allow upload for processing
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:20',
            'specialty' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $sourcePath = $file->getRealPath();
            $extension = strtolower($file->getClientOriginalExtension());
            $image = null;

            // Load image based on type
            if ($extension === 'jpeg' || $extension === 'jpg') {
                $image = @imagecreatefromjpeg($sourcePath);
            } elseif ($extension === 'png') {
                $image = @imagecreatefrompng($sourcePath);
            } elseif ($extension === 'webp') {
                $image = @imagecreatefromwebp($sourcePath);
            }

            if ($image) {
                // Process Image (Resize & Convert to WebP)
                $width = imagesx($image);
                $height = imagesy($image);
                $maxDim = 800; // Optimal web quality

                if ($width > $maxDim || $height > $maxDim) {
                    $ratio = $width / $height;
                    if ($ratio > 1) {
                        $newWidth = $maxDim;
                        $newHeight = $maxDim / $ratio;
                    } else {
                        $newHeight = $maxDim;
                        $newWidth = $maxDim * $ratio;
                    }

                    $newImage = imagecreatetruecolor((int) $newWidth, (int) $newHeight);

                    // Preserve transparency
                    if ($extension === 'png' || $extension === 'webp') {
                        imagealphablending($newImage, false);
                        imagesavealpha($newImage, true);
                        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                        imagefilledrectangle($newImage, 0, 0, (int) $newWidth, (int) $newHeight, $transparent);
                    }

                    imagecopyresampled($newImage, $image, 0, 0, 0, 0, (int) $newWidth, (int) $newHeight, $width, $height);
                    imagedestroy($image);
                    $image = $newImage;
                }

                // Generate Filename
                $filename = 'avatars/'.uniqid().'.webp';
                $fullPath = storage_path('app/public/'.$filename);

                // Ensure directory exists
                if (! file_exists(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }

                // Save as WebP with 80% quality
                imagewebp($image, $fullPath, 80);
                imagedestroy($image);

                // Clean up old avatar
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $user->avatar = $filename;
            } else {
                // Fallback if GD fails or format unsupported
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $path = $file->store('avatars', 'public');
                $user->avatar = $path;
            }
        }

        $user->save();

        $profile->update([
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_number' => $request->emergency_contact_number,
            'specialty' => $request->specialty,
            'bio' => $request->bio,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
