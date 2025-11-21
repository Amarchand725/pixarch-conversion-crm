<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('LeadCapture') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">

                <h1 class="text-2xl font-bold mb-4">Edit LeadCapture</h1>

                <form action="{{ route('leadCaptures.update', $leadCapture->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name', $leadCapture->name) }}" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full">
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700">Status</label>
                        <select name="status" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full">
                            <option value="active" {{ (old('status', $leadCapture->status) === 'active') ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ (old('status', $leadCapture->status) === 'inactive') ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>