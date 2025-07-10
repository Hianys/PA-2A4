<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Modifier le segment</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6">
        <form method="POST" action="{{ route('admin.segments.update', $segment->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label>Ville de départ</label>
                <x-text-input name="from_city" value="{{ old('from_city', $segment->from_city) }}" class="mt-1 w-full" required/>
            </div>

            <div class="mb-4">
                <label>Ville d’arrivée</label>
                <x-text-input name="to_city" value="{{ old('to_city', $segment->to_city) }}" class="mt-1 w-full" required/>
            </div>

            <div class="mb-4">
                <label>Statut</label>
                <select name="status" class="mt-1 w-full border-gray-300 rounded">
                    @foreach(['en attente', 'accepté', 'refusé'] as $status)
                        <option value="{{ $status }}" {{ old('status', $segment->status) == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.segments.show', $segment->id) }}" class="text-gray-600 hover:underline">
                    Annuler
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
