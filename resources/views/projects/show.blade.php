<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $project->name }}</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.edit', $project) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase tracking-widest rounded-md hover:bg-gray-700">
                    Edit
                </a>

                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                    onsubmit="return confirm('¿Seguro que quieres eliminar este proyecto?')">
                    @csrf
                    @method('DELETE')
                    <x-danger-button>Delete</x-danger-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                    x-transition.opacity
                    class="fixed top-20 right-6 z-50 flex items-center gap-2 px-4 py-3 bg-green-600 text-white text-sm rounded-lg shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ session('status') }}
                </div>
            @endif
            @can('update', $project)
                <div class="flex items-center gap-3">
                    {{-- aquí van tal cual el enlace Edit y el formulario Delete --}}
                </div>
            @endcan

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Description</h3>
                    <p class="mt-1 text-gray-900">{{ $project->description ?? 'No description.' }}</p>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Status</h3>
                        <p class="mt-1 text-gray-900">{{ $project->status }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Start date</h3>
                        <p class="mt-1 text-gray-900">{{ $project->start_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Due date</h3>
                        <p class="mt-1 text-gray-900">{{ $project->due_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500">Owner</h3>
                    <p class="mt-1 text-gray-900">{{ $project->user->name }}</p>
                </div>

                <div class="pt-4">
                    <a href="{{ route('projects.index') }}" class="text-gray-600 hover:underline">← Back to projects</a>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-white shadow-sm sm:rounded-lg p-6 mt-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Participants</h3>
            <span class="text-sm text-gray-500">{{ $project->participants->count() + 1 }} people</span>
        </div>

        {{-- Lista --}}
        <ul class="mt-4 divide-y divide-gray-100">
            {{-- Propietario --}}
            <li class="py-3 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">
                    {{ mb_strtoupper(mb_substr($project->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-gray-900 font-medium truncate">{{ $project->user->name }}</p>
                    <p class="text-sm text-gray-500 truncate">{{ $project->user->email }}</p>
                </div>
                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">Owner</span>
            </li>

            @foreach ($project->participants as $participant)
                <li class="py-3 flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-semibold">
                        {{ mb_strtoupper(mb_substr($participant->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-gray-900 font-medium truncate">{{ $participant->name }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ $participant->email }}</p>
                    </div>

                    @can('manageParticipants', $project)
                        {{-- Cambiar rol al instante --}}
                        <form method="POST" action="{{ route('projects.participants.update', [$project, $participant]) }}">
                            @csrf
                            @method('PATCH')
                            <select name="role" onchange="this.form.submit()"
                                class="text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="member" @selected($participant->pivot->role === 'member')>Member</option>
                                <option value="viewer" @selected($participant->pivot->role === 'viewer')>Viewer</option>
                            </select>
                        </form>

                        {{-- Botón que abre el modal --}}
                        <button type="button" title="Remove" x-data
                            x-on:click.prevent="$dispatch('open-modal', 'remove-participant-{{ $participant->id }}')"
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.74 9l-.35 9m-4.78 0L9.26 9m9.97-3.21c.34.05.68.11 1.02.17m-1.02-.17L18.16 19.67a2.25 2.25 0 01-2.24 2.08H8.08a2.25 2.25 0 01-2.24-2.08L4.77 5.79m14.46 0a48.1 48.1 0 00-3.48-.4m-12 .56c.34-.06.68-.12 1.02-.17m0 0a48.11 48.11 0 013.48-.4m7.5 0v-.92c0-1.18-.91-2.16-2.09-2.2a51.96 51.96 0 00-3.32 0c-1.18.04-2.09 1.02-2.09 2.2v.92m7.5 0a48.67 48.67 0 00-7.5 0" />
                            </svg>
                        </button>

                        {{-- Modal de confirmación --}}
                        <x-modal name="remove-participant-{{ $participant->id }}" focusable>
                            <form method="POST" action="{{ route('projects.participants.destroy', [$project, $participant]) }}"
                                class="p-6">
                                @csrf
                                @method('DELETE')

                                <h2 class="text-lg font-semibold text-gray-900">Remove participant?</h2>
                                <p class="mt-2 text-sm text-gray-600">
                                    <strong>{{ $participant->name }}</strong> will lose access to this project.
                                </p>

                                <div class="mt-6 flex justify-end gap-3">
                                    <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                    <x-danger-button>Remove</x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    @else
                        <span
                            class="px-2.5 py-1 text-xs font-medium rounded-full
                                {{ $participant->pivot->role === 'member' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($participant->pivot->role) }}
                        </span>
                    @endcan
                </li>
            @endforeach
        </ul>

        {{-- Formulario para añadir --}}
        @can('manageParticipants', $project)
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h4 class="text-sm font-medium text-gray-700">Add participant</h4>

                @if ($availableUsers->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">There are no more users to add.</p>
                @else
                    <form method="POST" action="{{ route('projects.participants.store', $project) }}" x-data="{
                                  open: false, search: '', selected: null,
                                  users: @js($availableUsers),
                                  get filtered() {
                                      const q = this.search.toLowerCase();
                                      return this.users.filter(u => (u.name + ' ' + u.email).toLowerCase().includes(q));
                                  },
                                  pick(u) { this.selected = u; this.open = false; this.search = ''; }
                              }" class="mt-3 flex flex-col sm:flex-row sm:items-start gap-3">
                        @csrf
                        <input type="hidden" name="user_id" :value="selected ? selected.id : ''">

                        {{-- Desplegable con buscador --}}
                        <div class="relative flex-1" @click.outside="open = false">
                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-left text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                <span x-text="selected ? selected.name + ' · ' + selected.email : 'Select a user…'"
                                    :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition x-cloak
                                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg">
                                <input type="text" x-model="search" placeholder="Search by name or email…"
                                    class="w-full px-3 py-2 text-sm border-0 border-b border-gray-200 rounded-t-md focus:ring-0">
                                <ul class="max-h-56 overflow-y-auto py-1">
                                    <template x-for="u in filtered" :key="u.id">
                                        <li @click="pick(u)"
                                            class="px-3 py-2 flex items-center gap-3 cursor-pointer hover:bg-indigo-50">
                                            <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-sm font-semibold"
                                                x-text="u.name.charAt(0).toUpperCase()"></div>
                                            <div class="min-w-0">
                                                <p class="text-sm text-gray-900 truncate" x-text="u.name"></p>
                                                <p class="text-xs text-gray-500 truncate" x-text="u.email"></p>
                                            </div>
                                        </li>
                                    </template>
                                    <li x-show="filtered.length === 0" class="px-3 py-2 text-sm text-gray-500">No users found.
                                    </li>
                                </ul>
                            </div>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>

                        {{-- Rol como botones --}}
                        <div class="inline-flex rounded-md shadow-sm">
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="member" class="peer sr-only" checked>
                                <span
                                    class="block px-4 py-2 text-sm border border-gray-300 rounded-l-md bg-white text-gray-700
                                                 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600">
                                    Member
                                </span>
                            </label>
                            <label class="cursor-pointer -ml-px">
                                <input type="radio" name="role" value="viewer" class="peer sr-only">
                                <span
                                    class="block px-4 py-2 text-sm border border-gray-300 rounded-r-md bg-white text-gray-700
                                                 peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600">
                                    Viewer
                                </span>
                            </label>
                        </div>

                        <x-primary-button x-bind:disabled="!selected"
                            x-bind:class="!selected && 'opacity-50 cursor-not-allowed'">
                            Add
                        </x-primary-button>
                    </form>
                @endif
            </div>
        @endcan
    </div>
</x-app-layout>