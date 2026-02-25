<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ApiTokenController extends Controller
{
    public function index()
    {
        return Inertia::render('Profile/ApiTokens', [
            'tokens' => Auth::user()->tokens()->latest()->get()->map(fn($t) => [
                'id'         => $t->id,
                'name'       => $t->name,
                'last_used'  => $t->last_used_at?->diffForHumans() ?? 'Never',
                'created_at' => $t->created_at->format('Y-m-d H:i'),
            ])
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token_name' => 'required|string|max:255',
        ]);

        $token = Auth::user()->createToken($request->token_name);

        return back()->with('success', 'Token created! Please copy it now, it won\'t be shown again: ' . $token->plainTextToken);
    }

    public function destroy($id)
    {
        Auth::user()->tokens()->where('id', $id)->delete();
        return back()->with('success', 'Token revoked.');
    }
}
