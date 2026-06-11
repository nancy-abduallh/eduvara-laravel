<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'student')
            ->withCount(['videos', 'quizAttempts'])
            ->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['videos', 'quizAttempts', 'latestVark']);
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate(['role' => 'in:student,admin', 'learning_style' => 'nullable|in:visual,auditory,reading,kinesthetic']);
        $user->update($request->only(['role', 'learning_style', 'proficiency_level']));
        return back()->with('success', 'User updated.');
    }
}
