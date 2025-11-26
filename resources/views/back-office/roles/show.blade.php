<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Role') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">

                <h1 class="text-2xl font-bold mb-4">View Role</h1>

                <div class="mb-4">
                    <strong>ID:</strong> {{ $role->id }}
                </div>

                <div class="mb-4">
                    <strong>Name:</strong> {{ $role->name ?? '-' }}
                </div>

                <div class="mb-4">
                    <strong>Status:</strong> {{ $role->status ?? '-' }}
                </div>

                <a href="{{ route('roles.edit', $role->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Edit</a>
            </div>
        </div>
    </div>
</x-app-layout>