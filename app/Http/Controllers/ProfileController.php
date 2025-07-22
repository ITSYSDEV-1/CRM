<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roles = $user->getRoleNames(); // Menggunakan Spatie Permission
        
        return view('profile.index', compact('user', 'roles'));
    }
    
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);
        
        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        
        return back()->with('success', 'Password berhasil diubah!');
    }
    
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);
        
        $user = Auth::user();
        $user->update($request->only(['name', 'email']));
        
        return back()->with('success', 'Profile berhasil diupdate!');
    }
}