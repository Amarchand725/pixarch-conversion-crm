<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('LeadCapture') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">

                <h1 class="text-2xl font-bold mb-4">View LeadCapture</h1>

                <div class="mb-4">
                    <strong>ID:</strong> {{ $leadCapture->id }}
                </div>

                <div class="mb-4">
                    <strong>Name:</strong> {{ $leadCapture->name ?? '-' }}
                </div>

                <div class="mb-4">
                    <strong>Status:</strong> {{ $leadCapture->status ?? '-' }}
                </div>

                <a href="{{ route('leadCaptures.edit', $leadCapture->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Edit</a>
            </div>
        </div>
    </div>
</x-app-layout>