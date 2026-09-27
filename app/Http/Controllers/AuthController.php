<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show login page.
     */
    public function showLogin()
    {
        if (session('user_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login via raw SQL.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Raw SQL query to fetch active user
        $users = DB::select("
            SELECT id, uuid, name, email, password, status, phone, avatar
            FROM users
            WHERE email = ? AND deleted_at IS NULL
            LIMIT 1
        ", [$request->email]);

        if (empty($users)) {
            return back()->withInput()->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        $user = $users[0];

        if ($user->status !== 'ACTIVE') {
            return back()->withInput()->withErrors(['email' => 'Your account is currently inactive. Please contact your administrator.']);
        }

        if (!Hash::check($request->password, $user->password) && !password_verify($request->password, $user->password)) {
            audit_log('LOGIN_FAILED', 'Auth', 'User', $user->id, null, ['email' => $request->email]);
            return back()->withInput()->withErrors(['password' => 'The provided password is incorrect.']);
        }

        // Set session
        session([
            'user_id' => $user->id,
            'user_uuid' => $user->uuid,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_avatar' => $user->avatar,
        ]);

        // Update login stats via raw SQL
        DB::update("
            UPDATE users
            SET last_login_at = NOW(), last_login_ip = ?, updated_at = NOW()
            WHERE id = ?
        ", [$request->ip(), $user->id]);

        audit_log('LOGIN', 'Auth', 'User', $user->id, null, ['ip' => $request->ip()]);

        return redirect()->intended(route('dashboard'))->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        $userId = session('user_id');
        $locale = session('locale', 'km');
        if ($userId) {
            audit_log('LOGOUT', 'Auth', 'User', $userId);
        }

        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session(['locale' => $locale]);

        return redirect()->route('login')->with('success', 'You have been successfully logged out.');
    }

    /**
     * Show profile page.
     */
    public function profile()
    {
        $userId = session('user_id');
        $users = DB::select("
            SELECT u.*, r.name as role_name
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.id = ? AND u.deleted_at IS NULL
            LIMIT 1
        ", [$userId]);

        if (empty($users)) {
            return redirect()->route('login');
        }

        $user = $users[0];

        // Fetch recent activities of this user
        $activities = DB::select("
            SELECT * FROM audit_logs
            WHERE user_id = ?
            ORDER BY id DESC
            LIMIT 10
        ", [$userId]);

        return view('auth.profile', compact('user', 'activities'));
    }

    /**
     * Update profile details via raw SQL.
     */
    public function updateProfile(Request $request)
    {
        $userId = session('user_id');
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $old = DB::select("SELECT name, phone, avatar FROM users WHERE id = ? LIMIT 1", [$userId])[0];

        $avatarPath = $old->avatar;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            session(['user_avatar' => $avatarPath]);
        }

        DB::update("
            UPDATE users
            SET name = ?, phone = ?, avatar = ?, updated_at = NOW()
            WHERE id = ?
        ", [$request->name, $request->phone, $avatarPath, $userId]);

        session(['user_name' => $request->name]);

        audit_log('UPDATE', 'Auth', 'User', $userId, $old, [
            'name' => $request->name,
            'phone' => $request->phone,
            'avatar' => $avatarPath
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Change password via raw SQL.
     */
    public function changePassword(Request $request)
    {
        $userId = session('user_id');
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = DB::select("SELECT password FROM users WHERE id = ? LIMIT 1", [$userId])[0];

        if (!Hash::check($request->current_password, $user->password) && !password_verify($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $newHashedPassword = Hash::make($request->password);

        DB::update("
            UPDATE users
            SET password = ?, updated_at = NOW()
            WHERE id = ?
        ", [$newHashedPassword, $userId]);

        audit_log('UPDATE_PASSWORD', 'Auth', 'User', $userId);

        return back()->with('success', 'Password changed successfully.');
    }

    /**
     * Switch application language between English and Khmer.
     */
    public function switchLanguage($lang)
    {
        $locale = in_array($lang, ['km', 'en']) ? $lang : 'km';
        session()->put('locale', $locale);
        session()->save();
        cookie()->queue(cookie()->forever('locale', $locale));
        app()->setLocale($locale);

        return redirect()->back(fallback: route('dashboard'));
    }
}

