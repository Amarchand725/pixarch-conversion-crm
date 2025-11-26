<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('LeadCapture') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">

                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-bold">All LeadCapture</h1>
                    <a href="{{ route('leadCaptures.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add New</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Name</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($leadCaptures ?? [] as $leadCapture)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $leadCapture->id }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $leadCapture->name ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $leadCapture->status ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        <a href="{{ route('leadCaptures.edit', $leadCapture->id) }}" class="text-blue-600">Edit</a>
                                        <a href="{{ route('leadCaptures.show', $leadCapture->id) }}" class="ml-2 text-green-600">View</a>
                                        <form action="{{ route('leadCaptures.destroy', $leadCapture->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-2 text-red-600">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>