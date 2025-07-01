<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">@lang("Admin Dashboard")</h2>
    </x-slot>

    <x-admin-content>
        <h2 class="text-2xl font-semibold mb-6">@lang("User List")</h2>

        <table class="w-full table-auto bg-white shadow rounded">
            <thead class="bg-gray-100 text-sm text-gray-700">
            <tr>
                <th class="px-4 py-4 text-left">ID</th>
                <th class="px-4 py-4 text-left">@lang("Name")</th>
                <th class="px-1 py-4 text-left">@lang("Email")</th>
                <th class="px-6 py-4 text-left">@lang("Role")</th>
                <th class="px-6 py-4 text-center">@lang("Actions")</th>
            </tr>
            </thead>
            <tbody class="text-sm text-gray-800">
            @foreach ($users as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-left">{{ $user->id }}</td>
                    <td class="px-6 py-4 text-left">{{ $user->name }}</td>
                    <td class="px-6 py-4 text-left">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-left capitalize">{{ $user->role }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="space-y-1">
                            @if ($user->role !== 'admin')
                                <form action="{{ route('admin.promote', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-green-600 hover:underline">@lang("Promote")</button>
                                </form>
                            @endif

                            @if ($user->role !== 'client')
                                <form action="{{ route('admin.demote', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-yellow-600 hover:underline">@lang("Demote")</button>
                                </form>
                            @endif

                            <form action="{{ route('admin.delete', $user->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">@lang("Delete")</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>



    </x-admin-content>
</x-app-layout>
