<a onclick="openEditModal({{ $row->id }})" class="text-blue-600">Edit</a>
<a onclick="openShowModal({{ $row->id }})" class="ml-2 text-green-600">View</a>
<form action="{{ route('{{ modulePlural }}.destroy', $row->id) }}" method="POST" class="inline">
    @csrf @method('DELETE')
    <button class="ml-2 text-red-600">Delete</button>
</form>
