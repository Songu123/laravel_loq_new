<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy
{
    /**
     * Determine whether the user can view any exams.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view exam lists
    }

    /**
     * Determine whether the user can view the exam.
     */
    public function view(User $user, Exam $exam): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can view their own exams
        if ($user->isTeacher() && $exam->created_by === $user->id) {
            return true;
        }

        // Students can view public and active exams
        if ($user->isStudent() && $exam->is_public && $exam->is_active) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create exams.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the exam.
     */
    public function update(User $user, Exam $exam): bool
    {
        // Admin can update all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can only update their own exams
        return $user->isTeacher() && $exam->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the exam.
     */
    public function delete(User $user, Exam $exam): bool
    {
        // Admin can delete all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can only delete their own exams if no attempts exist
        if ($user->isTeacher() && $exam->created_by === $user->id) {
            return $exam->attempts()->count() === 0;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the exam.
     */
    public function restore(User $user, Exam $exam): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the exam.
     */
    public function forceDelete(User $user, Exam $exam): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can duplicate the exam.
     */
    public function duplicate(User $user, Exam $exam): bool
    {
        // Admin can duplicate all
        if ($user->isAdmin()) {
            return true;
        }

        // Teacher can duplicate their own exams or public exams
        if ($user->isTeacher()) {
            return $exam->created_by === $user->id || $exam->is_public;
        }

        return false;
    }

    /**
     * Determine whether the user can take the exam.
     */
    public function take(User $user, Exam $exam): bool
    {
        // Only students can take exams
        if (!$user->isStudent()) {
            return false;
        }

        // Exam must be active and public
        if (!$exam->is_active || !$exam->is_public) {
            return false;
        }

        // Check time constraints
        if ($exam->start_time && now()->lt($exam->start_time)) {
            return false;
        }

        if ($exam->end_time && now()->gt($exam->end_time)) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can toggle exam status.
     */
    public function toggleStatus(User $user, Exam $exam): bool
    {
        return $this->update($user, $exam);
    }
}
