<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileUpdateRequest;

use function Laravel\Prompts\password;
use function PHPUnit\Framework\returnSelf;

class ProfileController extends Controller
{
    public function index() 
    {
        $user = auth()->user();
        return view('user.dashboard.profile', compact( 'user'));
    }

    public function showProfileEditForm() 
    {
        $user = auth()->user();
        return view('user.dashboard.edit-profile', compact('user'));
    }

    public function updateProfile(ProfileUpdateRequest $request, User $user): RedirectResponse
    {
        // Get authenticated user
        $user = Auth::user();

        // Validate the incoming request data
        $validatedData = $request->validated();

        // Update user details
        $user->name = $validatedData['name'];
        $user->username = $validatedData['username'] ?? $user->username;
        $user->email = $validatedData['email'];
        $user->phone = $validatedData['phone'] ?? $user->phone;
        $user->address = $validatedData['address'] ?? $user->address;
        $user->city = $validatedData['city'] ?? $user->city;
        $user->state = $validatedData['state'] ?? $user->state;
        $user->postal_code = $validatedData['postal_code'] ?? $user->postal_code;
        $user->country = $validatedData['country'] ?? $user->country;
        $user->date_of_birth = $validatedData['date_of_birth'] ?? $user->date_of_birth;
        $user->gender = $validatedData['gender'] ?? $user->gender;

        // Handle file upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::delete('public/' . $user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Verify current password before allowing updates
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return redirect()->back()
                ->with('error', 'The current password is incorrect.')
                ->withInput();
        }

        // Update user's information
        $user->save();

        // Redirect back to the dashboard page with a success message
        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
}
