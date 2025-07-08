<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Détails annonce</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 space-y-6">
        <a href="{{ route('admin.annonces.index') }}"
           class="text-indigo-600 hover:underline text-sm">
            ← Retour à la liste des annonces
        </a>

        {{-- Informations annonce --}}
        <div class="bg-white shadow p-6 rounded space-y-2">
            <h3 class="text-lg font-bold">{{ $annonce->title }}</h3>
            <p><strong>Type :</strong> {{ ucfirst($annonce->type) }}</p>
            <p><strong>Statut :</strong> {{ ucfirst($annonce->status) }}</p>
            <p>
                <strong>Auteur :</strong>
                @if ($annonce->user)
                    <a href="{{ route('admin.users.show', $annonce->user->id) }}"
                       class="text-indigo-600 hover:underline">
                        {{ $annonce->user->name }} </a>({{ ucfirst($annonce->user->role) }})
                @else
                    N/A
                @endif
            </p>
            <p><strong>Date souhaitée :</strong> {{ $annonce->preferred_date }}</p>
            <p><strong>Description :</strong> {{ $annonce->description }}</p>
        </div>

        {{-- Actions --}}
        <div class="flex gap-4">
            <a href="{{ route('admin.annonces.edit', $annonce->id) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                Modifier
            </a>

            <form action="{{ route('admin.annonces.archive', $annonce->id) }}"
                  method="POST">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                    Archiver
                </button>
            </form>

            <form action="{{ route('admin.annonces.delete', $annonce->id) }}"
                  method="POST"
                  onsubmit="return confirm('Confirmer la suppression ?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                    Supprimer
                </button>
            </form>
        </div>

        {{-- Segments --}}
        @if ($annonce->type === 'transport' && $annonce->segments->count())
            <div class="bg-white shadow p-6 rounded mt-6">
                <h3 class="text-lg font-bold mb-4">Segments pris en charge</h3>
                <ul class="space-y-2">
                    @foreach ($annonce->segments as $segment)
                        <li class="border p-3 rounded flex justify-between">
                            <div>
                                <a href="{{ route('admin.segments.show', $segment->id) }}"
                                   class="font-semibold text-indigo-600 hover:underline">
                                    {{ $segment->from_city }} → {{ $segment->to_city }}
                                </a>
                                @if ($segment->delivery)
                                    <p class="text-sm text-gray-600">
                                        Par :
                                        <a href="{{ route('admin.users.show', $segment->delivery->id) }}"
                                           class="text-indigo-600 hover:underline">
                                            {{ $segment->delivery->name }}
                                        </a>
                                        ({{ ucfirst($segment->delivery->role) }})
                                    </p>
                                @endif
                            </div>
                            <span class="text-sm text-gray-500">
                                {{ ucfirst($segment->status) }}
                            </span>
                        </li>

                    @endforeach
                </ul>
            </div>
        @endif

    </div>
</x-app-layout>
