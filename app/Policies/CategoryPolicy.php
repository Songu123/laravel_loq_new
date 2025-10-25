<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine whether the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view categories
    }

    /**
     * Determine whether the user can view the category.
     */
    public function view(User $user, Category $category): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Teachers can only view their own categories
        if ($user->isTeacher()) {
            return $category->created_by === $user->id;
        }

        // Students can view active categories
        return $category->is_active;
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the category.
     */
    public function update(User $user, Category $category): bool
    {
        // Admin can update all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can only update their own categories
        return $user->isTeacher() && $category->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the category.
     */
    public function delete(User $user, Category $category): bool
    {
        // Admin can delete all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can only delete their own categories if no exams exist
        if ($user->isTeacher() && $category->created_by === $user->id) {
            return $category->exams()->count() === 0;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the category.
     */
    public function restore(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the category.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can toggle category status.
     */
    public function toggleStatus(User $user, Category $category): bool
    {
        // Only admin can toggle status
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can request approval for category.
     */
    public function requestApproval(User $user, Category $category): bool
    {
        // Teacher can request approval for their own categories
        return $user->isTeacher() && $category->created_by === $user->id;
    }
}
