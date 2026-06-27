<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Institution;
use App\Models\Organization;
use App\Models\Student;
use App\Models\Employer;
use App\Models\SkillCategory;
use App\Models\SkillProgram;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'         => User::count(),
            'total_institutions'  => Institution::count(),
            'total_organizations' => Organization::count(),
            'total_students'      => Student::count(),
            'total_employers'     => Employer::count(),
            'total_programs'      => SkillProgram::count(),
            'system_revenue'      => Payment::where('status', 'paid')->sum('amount'),
        ];

        $pendingInstitutions  = Institution::with('user')->where('status', 'pending')->get();
        $pendingOrganizations = Organization::with('user')->where('status', 'pending')->get();
        $pendingEmployers     = Employer::with('user')->where('status', 'pending')->get();

        $users = User::with('role')->orderBy('name')->get();
        $categories = SkillCategory::withCount('programs')->orderBy('name')->get();
        $auditLogs = AuditLog::with('user')->latest()->take(100)->get();

        // Chart Data (Simple aggregation for demonstration)
        $userGrowth = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data'   => [10, 25, 45, 80, 120, User::count()],
        ];

        $revenueTrends = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data'   => [0, 5000, 15000, 45000, 90000, Payment::where('status', 'paid')->sum('amount')],
        ];

        return view('dashboard.admin', compact(
            'stats',
            'pendingInstitutions',
            'pendingOrganizations',
            'pendingEmployers',
            'users',
            'categories',
            'auditLogs',
            'userGrowth',
            'revenueTrends'
        ));
    }

    public function approve($type, $id)
    {
        if ($type === 'institution') {
            $inst = Institution::findOrFail($id);
            $inst->update(['status' => 'active']);
            $inst->user->update(['status' => 'active']);
            AuditLog::log(Auth::id(), 'approve_institution', "Approved institution: {$inst->name}");
        } elseif ($type === 'organization') {
            $org = Organization::findOrFail($id);
            $org->update(['status' => 'active']);
            $org->user->update(['status' => 'active']);
            AuditLog::log(Auth::id(), 'approve_organization', "Approved organization: {$org->name}");
        }

        return back()->with('success', ucfirst($type) . ' approved successfully.');
    }

    public function approveEmployer($id)
    {
        $employer = Employer::findOrFail($id);
        $employer->update(['status' => 'active']);
        $employer->user->update(['status' => 'active']);
        AuditLog::log(Auth::id(), 'approve_employer', "Approved employer: {$employer->company_name}");
        return back()->with('success', "Employer '{$employer->company_name}' approved successfully.");
    }

    public function rejectEmployer($id)
    {
        $employer = Employer::findOrFail($id);
        $employer->update(['status' => 'rejected']);
        $employer->user->update(['status' => 'deactivated']);
        AuditLog::log(Auth::id(), 'reject_employer', "Rejected employer: {$employer->company_name}");
        return back()->with('success', "Employer '{$employer->company_name}' registration rejected.");
    }

    public function reject($type, $id)
    {
        if ($type === 'institution') {
            $inst = Institution::findOrFail($id);
            $inst->update(['status' => 'rejected']);
            $inst->user->update(['status' => 'deactivated']);
            AuditLog::log(Auth::id(), 'reject_institution', "Rejected institution: {$inst->name}");
        } elseif ($type === 'organization') {
            $org = Organization::findOrFail($id);
            $org->update(['status' => 'rejected']);
            $org->user->update(['status' => 'deactivated']);
            AuditLog::log(Auth::id(), 'reject_organization', "Rejected organization: {$org->name}");
        }

        return back()->with('success', ucfirst($type) . ' registration rejected.');
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $newStatus = $user->status === 'active' ? 'deactivated' : 'active';
        $user->update(['status' => $newStatus]);

        // Also update profile status if applicable
        if ($user->isInstitution() && $user->institution) {
            $user->institution->update(['status' => $newStatus]);
        } elseif ($user->isOrganization() && $user->organization) {
            $user->organization->update(['status' => $newStatus]);
        }

        AuditLog::log(Auth::id(), 'toggle_user_status', "Changed user status of {$user->name} to {$newStatus}");

        return back()->with('success', "User status updated to {$newStatus}.");
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:skill_categories,name',
            'icon' => 'nullable|string|max:50',
        ]);

        SkillCategory::create([
            'name' => $request->name,
            'icon' => $request->icon ?? '📁',
        ]);

        AuditLog::log(Auth::id(), 'create_category', "Created skill category: {$request->name}");

        return back()->with('success', 'Skill category created successfully.');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = SkillCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:skill_categories,name,' . $id,
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update([
            'name' => $request->name,
            'icon' => $request->icon ?? $category->icon,
        ]);

        AuditLog::log(Auth::id(), 'update_category', "Updated skill category ID {$id} to {$request->name}");

        return back()->with('success', 'Skill category updated successfully.');
    }

    public function destroyCategory($id)
    {
        $category = SkillCategory::findOrFail($id);
        $name = $category->name;
        $category->delete();

        AuditLog::log(Auth::id(), 'delete_category', "Deleted skill category: {$name}");

        return back()->with('success', 'Skill category deleted successfully.');
    }
}
