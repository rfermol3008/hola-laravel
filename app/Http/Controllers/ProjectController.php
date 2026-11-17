<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $projects = Project::with('user')
            ->where('user_id', $user->id)
            ->orWhereHas('participants', fn($q) => $q->where('users.id', $user->id))
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        $request->user()->projects()->create($data);

        return redirect()->route('projects.index')->with('status', 'Proyecto creado.');
    }

    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        $project->load('user', 'participants');

        $availableUsers = User::query()
            ->where('id', '!=', $project->user_id)
            ->whereNotIn('id', $project->participants->pluck('id'))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('projects.show', compact('project', 'availableUsers'));
    }

    public function edit(Project $project)
    {
        Gate::authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $data = $request->validate($this->rules());

        $project->update($data);

        return redirect()->route('projects.show', $project)->with('status', 'Proyecto actualizado.');
    }

    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Proyecto eliminado.');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,in_progress,done'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}