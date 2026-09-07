<?php

namespace App\Policies\Recruitment;

use App\Models\Recruitment\RecruitmentApplication;
use App\Models\User;

class RecruitmentApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user) || $user->can('recruitment.applications.list');
    }

    public function view(User $user, RecruitmentApplication $application): bool
    {
        return $this->isSuperAdmin($user) || $user->can('recruitment.applications.view');
    }

    public function downloadDocument(User $user, RecruitmentApplication $application): bool
    {
        return $this->view($user, $application);
    }

    public function screen(User $user, RecruitmentApplication $application): bool
    {
        return $this->isSuperAdmin($user) || $user->can('recruitment.screening.decide');
    }

    private function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super-admin');
    }
}
