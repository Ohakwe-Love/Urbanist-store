<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\returnSelf;

class ProfileController extends Controller
{
    public function index() 
    {
        return view('user.dashboard.profile');
    }

    public function showProfileEditForm() 
    {
        return view('user.dashboard.edit-profile');
    }

    // public function update(Request $request, User $user): RedirectResponse
    // {
    //     // Get authenticated user
    //     $user = Auth::user();

    //     // Validate the incoming request data
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
    //         'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     // Update user details
    //     $user->name = $validatedData['name'];
    //     $user->email = $validatedData['email'];

    //     // Handle file upload
    //     if ($request->hasFile('avatar')) {
    //         // Delete old avatar if exists
    //         if ($user->avatar) {
    //             Storage::delete('public/' . $user->avatar);
    //         }

    //         // Store new avatar
    //         $avatarPath = $request->file('avatar')->store('avatars', 'public');
    //         $user->avatar = $avatarPath;
    //     }

    //     // Update user's information
    //     $user->save();

    //     // Redirect back to the dashboard page with a success message
    //     return redirect()->route('dashboard.index')->with('success', 'Profile updated successfully.');
    // }
}
