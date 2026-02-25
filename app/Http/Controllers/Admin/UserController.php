<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('resumes')->latest()->paginate(20);
        return Inertia::render('Admin/Users', ['users' => $users]);
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot delete admin users.');
        }
        $user->delete();
        return back()->with('success', 'User deleted.');
    }

    public function toggleRole(User $user)
    {
        $user->update(['role' => $user->role === 'admin' ? 'user' : 'admin']);
        return back();
    }

    public function toggleActive(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot disable admin users.');
        }
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated.');
    }

    public function changePlan(Request $request, User $user)
    {
        $validated = $request->validate(['plan' => 'required|in:free,pro']);
        $user->update([
            'plan' => $validated['plan'],
            'subscription_ends_at' => $validated['plan'] === 'pro' ? now()->addMonth() : null,
        ]);
        return back();
    }

    public function impersonate(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot impersonate another admin.');
        }

        // Store current admin ID in session
        Session::put('impersonated_by', Auth::id());

        // Login as the user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Now impersonating {$user->name}");
    }

    public function leaveImpersonation()
    {
        if (!Session::has('impersonated_by')) {
            return redirect()->route('dashboard');
        }

        $adminId = Session::get('impersonated_by');
        $admin = User::find($adminId);

        if (!$admin) {
            Session::forget('impersonated_by');
            return redirect()->route('login');
        }

        Auth::login($admin);
        Session::forget('impersonated_by');

        return redirect()->route('admin.users.index')->with('success', 'Returned to admin view.');
    }
}
