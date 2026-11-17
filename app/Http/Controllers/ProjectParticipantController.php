<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectParticipantController extends Controller
{
    public function store(Request $request, Project $project)
    {
        Gate::authorize('manageParticipants', $project);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id', Rule::notIn([$project->user_id])],
            'role'    => ['required', 'in:member,viewer'],
        ]);

        $project->participants()->syncWithoutDetaching([
            $data['user_id'] => ['role' => $data['role']],
        ]);

        return back()->with('status', 'Participante añadido.');
    }

    public function update(Request $request, Project $project, User $user)
    {
        Gate::authorize('manageParticipants', $project);

        $data = $request->validate([
            'role' => ['required', 'in:member,viewer'],
        ]);

        $project->participants()->updateExistingPivot($user->id, ['role' => $data['role']]);

        return back()->with('status', 'Rol actualizado.');
    }

    public function destroy(Project $project, User $user)
    {
        Gate::authorize('manageParticipants', $project);

        $project->participants()->detach($user->id);

        return back()->with('status', 'Participante eliminado.');
    }
}