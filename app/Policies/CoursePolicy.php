<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function view(User $user, Course $course): bool
    {
        return $user->role === Role::ADMIN
            || $user->role === Role::GIAO_VU
            || ($user->role === Role::GIANG_VIEN && $course->giang_vien_id === $user->id);
    }

    public function manage(User $user, Course $course): bool
    {
        return $user->role === Role::ADMIN || $user->role === Role::GIAO_VU;
    }

    public function manageEnrollments(User $user, Course $course): bool
    {
        return $user->role === Role::ADMIN || $user->role === Role::GIAO_VU;
    }

    public function manageGrades(User $user, Course $course): bool
    {
        return $user->role === Role::ADMIN
            || $user->role === Role::GIAO_VU
            || ($user->role === Role::GIANG_VIEN && $course->giang_vien_id === $user->id);
    }
}
