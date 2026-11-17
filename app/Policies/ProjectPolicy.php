<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $user->id === $project->user_id
            || $project->participants()->where('users.id', $user->id)->exists();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }
    public function manageParticipants(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }
}