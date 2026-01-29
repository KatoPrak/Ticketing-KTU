<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    /**
     * Show universal change password form (untuk route universal)
     */
    public function showForm()
    {
        $user = Auth::user();
        
        // Redirect ke form yang sesuai berdasarkan role
        switch (strtolower($user->role)) {
            case 'admin':
                return redirect()->route('admin.dashboard'); // atau buat view admin
            case 'tim it':
            case 'it':
                return redirect()->route('it.password.form'); // redirect ke form IT
            case 'staff':
            case 'user':
                return view('auth.change_password'); // Default untuk staff/user
            default:
                return view('auth.change_password'); // Fallback
        }
    }

    /**
     * Handle universal password change (untuk route universal)
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:10|confirmed',
            'new_password_confirmation' => 'required'
        ], [
            'current_password.required' => 'Current password is required',
            'new_password.required' => 'New password is required',
            'new_password.min' => 'Password must be at least 10 characters',
            'new_password.confirmed' => 'Password confirmation does not match'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password must be different from current password'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        \Log::info('Password changed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'timestamp' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }

    /**
     * Show change password form khusus untuk IT
     */
    public function showChangePasswordForm()
    {
        return view('it.change-password'); // View khusus IT
    }

    /**
     * Handle password change khusus untuk IT
     */
    public function updatePassword(Request $request)
    {
        // Gunakan logika yang sama dengan update universal
        return $this->update($request);
    }
}