<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    /**
     * Get user's organizations
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $organizations = $user->organizations()
            ->with(['departments', 'roles'])
            ->wherePivot('is_active', true)
            ->get();

        return response()->json([
            'organizations' => $organizations
        ]);
    }

    /**
     * Create organization
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $organization = Organization::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'domain' => $request->domain,
            'is_active' => true,
        ]);

        return response()->json([
            'organization' => $organization,
            'message' => 'Organization created successfully'
        ], 201);
    }

    /**
     * Get organization departments
     */
    public function departments(Request $request, Organization $organization): JsonResponse
    {
        $user = $request->user();

        // Check if user belongs to organization
        if (!$user->organizations()->where('organization_id', $organization->id)->exists()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $departments = $organization->departments()
            ->with('parent')
            ->where('is_active', true)
            ->get();

        return response()->json([
            'departments' => $departments
        ]);
    }

    /**
     * Get organization roles
     */
    public function roles(Request $request, Organization $organization): JsonResponse
    {
        $user = $request->user();

        // Check if user belongs to organization
        if (!$user->organizations()->where('organization_id', $organization->id)->exists()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $roles = $organization->roles()
            ->where('is_active', true)
            ->orderBy('level', 'desc')
            ->get();

        return response()->json([
            'roles' => $roles
        ]);
    }

    /**
     * Get organization users
     */
    public function users(Request $request, Organization $organization): JsonResponse
    {
        $user = $request->user();

        // Check if user belongs to organization
        if (!$user->organizations()->where('organization_id', $organization->id)->exists()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $users = $organization->users()
            ->with(['departments', 'roles'])
            ->wherePivot('is_active', true)
            ->get();

        return response()->json([
            'users' => $users
        ]);
    }
}
