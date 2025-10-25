<?php

namespace App\Policies;

use App\Models\ClassRoom;
use App\Models\User;

class ClassRoomPolicy
{
    /**
     * Determine whether the user can view any classes.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view class lists
    }

    /**
     * Determine whether the user can view the class.
     */
    public function view(User $user, ClassRoom $class): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can view their own classes
        if ($user->isTeacher() && $class->teacher_id === $user->id) {
            return true;
        }

        // Student can view classes they've joined
        if ($user->isStudent()) {
            return $class->students()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create classes.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the class.
     */
    public function update(User $user, ClassRoom $class): bool
    {
        // Admin can update all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can only update their own classes
        return $user->isTeacher() && $class->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can delete the class.
     */
    public function delete(User $user, ClassRoom $class): bool
    {
        // Admin can delete all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can only delete their own classes
        return $user->isTeacher() && $class->teacher_id === $user->id;
    }

    /**
     * Determine whether the user can restore the class.
     */
    public function restore(User $user, ClassRoom $class): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the class.
     */
    public function forceDelete(User $user, ClassRoom $class): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can manage students in the class.
     */
    public function manageStudents(User $user, ClassRoom $class): bool
    {
        return $this->update($user, $class);
    }

    /**
     * Determine whether the user can manage exams in the class.
     */
    public function manageExams(User $user, ClassRoom $class): bool
    {
        return $this->update($user, $class);
    }

    /**
     * Determine whether the user can approve/reject join requests.
     */
    public function manageJoinRequests(User $user, ClassRoom $class): bool
    {
        return $this->update($user, $class);
    }

    /**
     * Determine whether the user can join the class.
     */
    public function join(User $user, ClassRoom $class): bool
    {
        // Only students can join classes
        if (!$user->isStudent()) {
            return false;
        }

        // Class must be active
        if (!$class->is_active) {
            return false;
        }

        // Student shouldn't already be in the class
        return !$class->students()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can leave the class.
     */
    public function leave(User $user, ClassRoom $class): bool
    {
        // Only students can leave
        if (!$user->isStudent()) {
            return false;
        }

        // Student must be in the class
        return $class->students()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can view exam results.
     */
    public function viewResults(User $user, ClassRoom $class): bool
    {
        return $this->update($user, $class);
    }
}
