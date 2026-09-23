<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class SupervisorManagementController extends Controller
{
    public function index()
    {
        // Only fetch users who are currently assigned as supervisors
        $supervisors = User::where('role', 'supervisor')
            ->orWhereNotNull('branch_id')
            ->with('branch')
            ->get();

        $branches = Branch::orderBy('name')->get();

        return view('admin.supervisors.manage', compact('supervisors', 'branches'));
    }

    public function assignBranch(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        $user = User::where('email', $validated['email'])->firstOrFail();

        $user->update([
            'role' => 'supervisor',
            'branch_id' => $validated['branch_id'],
        ]);

        $branch = Branch::find($validated['branch_id']);

        return back()->with('admin_status', "{$user->name} ({$user->email}) has been assigned as Supervisor for {$branch->name}.");
    }

    public function removeSupervisor(User $user)
    {
        $user->update([
            'role' => 'customer',
            'branch_id' => null,
        ]);

        return back()->with('admin_status', "Removed supervisor role for {$user->name}.");
    }
}